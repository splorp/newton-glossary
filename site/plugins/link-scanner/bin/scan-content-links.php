<?php

declare(strict_types=1);

$root = dirname(__DIR__, 4);
chdir($root);

require $root . '/kirby/bootstrap.php';

\Kirby\Sane\Svg::$allowedTags['svg'] = array_merge(\Kirby\Sane\Svg::$allowedAttrs, ['role', 'fill', 'aria-hidden']);

$kirby = new Kirby([
	'cli' => true,
]);

$scanner = new \ScottBoms\LinkScanner\Scanner($kirby);
$results = $scanner->scanAll();

(new \ScottBoms\LinkScanner\ScanStore($kirby))->save($results);

fwrite(STDOUT, "Broken links scan complete\n");
fwrite(STDOUT, "Broken links: " . $results['totalBrokenLinks'] . "\n");
fwrite(STDOUT, "Checked links: " . $results['totalCheckedLinks'] . "\n");
fwrite(STDOUT, "Scanned at: " . $results['scannedAt'] . "\n");
