<?php

declare(strict_types=1);

namespace ScottBoms\LinkScanner;

use Exception;
use Kirby\Cms\App;
use Throwable;

class ScanWorkerManager {
	public function __construct(
		protected App $kirby,
		protected string $rootPath
	) {
	}

	public function detachedCommand(): string {
		$phpBinary = $this->resolvePhpBinary();
		$script = $this->rootPath . '/bin/run-panel-scan.php';

		return escapeshellarg($phpBinary) . ' ' . escapeshellarg($script);
	}

	public function resolvePhpBinary(): string {
		$candidates = array_values(array_filter([
			PHP_BINDIR . '/php',
			dirname(PHP_BINARY) . '/php',
			PHP_BINARY,
			'php',
		]));

		foreach ($candidates as $candidate) {
			if ($candidate === 'php') {
				return $candidate;
			}

			if (is_file($candidate) === true && is_executable($candidate) === true) {
				return $candidate;
			}
		}

		throw new Exception('Could not resolve a CLI PHP binary for the background scan.');
	}

	public function startWorker(string $scanId): void {
		if (function_exists('exec') !== true) {
			throw new Exception('PHP exec() is not available on this server.');
		}

		$command = $this->detachedCommand() . ' ' . escapeshellarg($scanId) . ' > /dev/null 2>&1 &';
		exec($command);
	}

	public function terminateWorker(int $pid): bool {
		if ($pid <= 0) {
			return false;
		}

		if (function_exists('posix_kill') === true && defined('SIGTERM') === true) {
			try {
				if (posix_kill($pid, SIGTERM) === true) {
					return true;
				}
			} catch (Throwable) {
			}
		}

		if (function_exists('exec') === true) {
			$command = 'kill ' . (int)$pid . ' > /dev/null 2>&1';
			exec($command, $output, $code);
			return $code === 0;
		}

		return false;
	}

	public function awaitWorkerStart(ScanStore $store, string $scanId): array {
		for ($attempt = 0; $attempt < 10; $attempt++) {
			usleep(200000);
			$current = $store->current();

			if (($current['id'] ?? null) !== $scanId) {
				break;
			}

			if (
				($current['workerStartedAt'] ?? null) !== null ||
				($current['processedPages'] ?? 0) > 0 ||
				($current['currentPageTitle'] ?? null) !== null ||
				($current['lastError'] ?? null) !== null ||
				($current['isRunning'] ?? false) === false
			) {
				return $current;
			}
		}

		$current = $store->current();

		if (($current['id'] ?? null) === $scanId && ($current['workerStartedAt'] ?? null) === null) {
			$current['isRunning'] = false;
			$current['lastError'] = 'The background scan worker did not start. Check server exec() permissions and CLI PHP availability.';
			$store->saveCurrent($current);
		}

		return $store->current();
	}
}
