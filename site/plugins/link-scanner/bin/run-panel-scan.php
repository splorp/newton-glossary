<?php

declare(strict_types=1);

$scanId = $argv[1] ?? null;

if (is_string($scanId) !== true || trim($scanId) === '') {
	fwrite(STDERR, "Missing scan id\n");
	exit(1);
}

$root = dirname(__DIR__, 4);
chdir($root);

require $root . '/kirby/bootstrap.php';

\Kirby\Sane\Svg::$allowedTags['svg'] = array_merge(\Kirby\Sane\Svg::$allowedAttrs, ['role', 'fill', 'aria-hidden']);

$kirby = new Kirby([
	'cli' => true,
]);

$store = new \ScottBoms\LinkScanner\ScanStore($kirby);
$current = $store->current();
$workerPid = getmypid() ?: null;

if (($current['id'] ?? null) !== $scanId) {
	fwrite(STDERR, "Scan not found\n");
	exit(1);
}

$scanner = new \ScottBoms\LinkScanner\Scanner($kirby);
$results = [];

$stopScan = static function () use ($store, $scanId): void {
	$current = $store->current();

	if (($current['id'] ?? null) !== $scanId) {
		exit(0);
	}

	$current['isRunning'] = false;
	$current['isComplete'] = false;
	$current['cancelRequested'] = false;
	$current['currentPageTitle'] = null;
	$current['stoppedAt'] = date(DATE_ATOM);
	$current['lastError'] = null;
	$store->saveCurrent($current);
	exit(0);
};

if (function_exists('pcntl_async_signals') === true && function_exists('pcntl_signal') === true) {
	pcntl_async_signals(true);
	pcntl_signal(SIGTERM, $stopScan);
	pcntl_signal(SIGINT, $stopScan);
}

$current['isRunning'] = true;
$current['isComplete'] = false;
$current['cancelRequested'] = false;
$current['workerPid'] = $workerPid;
$current['stoppedAt'] = null;
$current['workerStartedAt'] = date(DATE_ATOM);
$current['lastError'] = null;
$store->saveCurrent($current);

try {
	foreach ($scanner->getPageQueue() as $page) {
		$current = $store->current();

		if (($current['id'] ?? null) !== $scanId) {
			throw new RuntimeException('Scan state changed before completion.');
		}

		if (($current['cancelRequested'] ?? false) === true) {
			$stopScan();
		}

		$current['currentPageTitle'] = $page['title'];
		$store->saveCurrent($current);

		$step = $scanner->scanRecord($page);
		$results = [
			...$results,
			...$step['results'],
		];

		$current['processedPages'] = (int)($current['processedPages'] ?? 0) + 1;
		$current['currentPageTitle'] = null;
		$store->saveCurrent($current);
	}

	$summary = $scanner->buildSummary($results);
	$store->save($summary);

	$current = $store->current();
	$current['isRunning'] = false;
	$current['isComplete'] = true;
	$current['cancelRequested'] = false;
	$current['workerPid'] = null;
	$current['finishedAt'] = date(DATE_ATOM);
	$current['currentPageTitle'] = null;
	$current['lastError'] = null;
	$store->saveCurrent($current);
} catch (\Throwable $exception) {
	$current = $store->current();
	$current['isRunning'] = false;
	$current['isComplete'] = false;
	$current['cancelRequested'] = false;
	$current['workerPid'] = null;
	$current['currentPageTitle'] = null;
	$current['lastError'] = $exception->getMessage();
	$store->saveCurrent($current);
	
	fwrite(STDERR, $exception->getMessage() . "\n");
	exit(1);
}
