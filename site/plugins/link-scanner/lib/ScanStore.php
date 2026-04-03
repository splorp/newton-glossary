<?php

declare(strict_types=1);

namespace ScottBoms\LinkScanner;

use Kirby\Cms\App;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;

class ScanStore {
	public function __construct(protected App $kirby) {
	}

	public function latest(): array {
		$cached = $this->readJson($this->latestPath());

		if (is_array($cached) === true) {
			return $this->normalizeLatest($cached);
		}
		return $this->emptyLatest();
	}

	public function save(array $results): void {
		$this->writeJson($this->latestPath(), $results);
	}

	public function completeResult(array $result): array {
		$latest = $this->latest();
		$results = $latest['results'] ?? [];
		$match = $this->resultKey($result);
		$removed = false;

		$results = array_values(array_filter($results, function (array $row) use ($match, &$removed): bool {
			if ($removed === false && $this->resultKey($row) === $match) {
				$removed = true;
				return false;
			}

			return true;
		}));

		if ($removed === false) {
			return $latest;
		}

		$latest['results'] = $results;
		$latest['totalBrokenLinks'] = count($results);
		$this->save($latest);

		return $this->latest();
	}

	public function current(): array {
		$cached = $this->readJson($this->currentPath());

		if (is_array($cached) === true) {
			return $this->normalizeCurrent($cached);
		}
		return $this->emptyCurrent();
	}

	public function saveCurrent(array $scan): void {
		$scan['updatedAt'] = date(DATE_ATOM);
		$this->writeJson($this->currentPath(), $this->normalizeCurrent($scan));
	}

	protected function basePath(): string {
		$path = $this->preferredBasePath();

		if (is_dir($path) !== true) {
			Dir::make($path, true);
		}
		return $path;
	}

	protected function preferredBasePath(): string {
		return $this->kirby->root('cache') . '/link-scanner';
	}

	protected function legacyBasePath(): string {
		return $this->kirby->root('cache') . '/links-scanner';
	}

	protected function latestPath(): string {
		return $this->basePath() . '/latest.json';
	}

	protected function currentPath(): string {
		return $this->basePath() . '/current.json';
	}

	protected function readJson(string $path): ?array {
		if (F::exists($path) !== true) {
			$legacy = $this->legacyPathFromPreferred($path);

			if ($legacy === null || F::exists($legacy) !== true) {
				return null;
			}

			$this->migrateLegacyFile($legacy, $path);
		}

		if (F::exists($path) !== true) {
			return null;
		}

		$json = F::read($path);
		$data = json_decode($json, true);
		return is_array($data) === true ? $data : null;
	}

	protected function writeJson(string $path, array $data): void {
		F::write($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}

	protected function legacyPathFromPreferred(string $path): ?string {
		$preferred = $this->preferredBasePath();

		if (str_starts_with($path, $preferred) !== true) {
			return null;
		}

		return $this->legacyBasePath() . substr($path, strlen($preferred));
	}

	protected function migrateLegacyFile(string $legacyPath, string $preferredPath): void {
		$content = F::read($legacyPath);

		if (is_string($content) !== true || $content === '') {
			return;
		}

		F::write($preferredPath, $content);
	}

	protected function resultKey(array $result): string {
		return implode('|', [
			(string)($result['url'] ?? ''),
			(string)($result['pageTitle'] ?? ''),
			(string)($result['panelUrl'] ?? ''),
			(string)($result['reason'] ?? ''),
		]);
	}

	protected function emptyLatest(): array {
		return [
			'hasScanned' => false,
			'scannedAt' => null,
			'totalBrokenLinks' => 0,
			'totalCheckedLinks' => 0,
			'results' => [],
		];
	}

	protected function normalizeLatest(array $cached): array {
		return [
			'hasScanned' => (bool)($cached['hasScanned'] ?? false),
			'scannedAt' => $cached['scannedAt'] ?? null,
			'totalBrokenLinks' => (int)($cached['totalBrokenLinks'] ?? 0),
			'totalCheckedLinks' => (int)($cached['totalCheckedLinks'] ?? 0),
			'results' => array_values($cached['results'] ?? []),
		];
	}

	protected function emptyCurrent(): array {
		return [
			'id' => null,
			'isRunning' => false,
			'isComplete' => false,
			'cancelRequested' => false,
			'workerPid' => null,
			'startedAt' => null,
			'finishedAt' => null,
			'stoppedAt' => null,
			'workerStartedAt' => null,
			'processedPages' => 0,
			'totalPages' => 0,
			'currentPageTitle' => null,
			'lastError' => null,
			'updatedAt' => null,
		];
	}

	protected function normalizeCurrent(array $cached): array {
		return [
			'id' => $cached['id'] ?? null,
			'isRunning' => (bool)($cached['isRunning'] ?? false),
			'isComplete' => (bool)($cached['isComplete'] ?? false),
			'cancelRequested' => (bool)($cached['cancelRequested'] ?? false),
			'workerPid' => isset($cached['workerPid']) ? (int)$cached['workerPid'] : null,
			'startedAt' => $cached['startedAt'] ?? null,
			'finishedAt' => $cached['finishedAt'] ?? null,
			'stoppedAt' => $cached['stoppedAt'] ?? null,
			'workerStartedAt' => $cached['workerStartedAt'] ?? null,
			'processedPages' => (int)($cached['processedPages'] ?? 0),
			'totalPages' => (int)($cached['totalPages'] ?? 0),
			'currentPageTitle' => $cached['currentPageTitle'] ?? null,
			'lastError' => $cached['lastError'] ?? null,
			'updatedAt' => $cached['updatedAt'] ?? null,
		];
	}
}
