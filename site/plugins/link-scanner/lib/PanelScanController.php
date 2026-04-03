<?php

declare(strict_types=1);

namespace ScottBoms\LinkScanner;

use Exception;
use Kirby\Cms\App;
use Throwable;

class PanelScanController {
	protected ScanWorkerManager $workerManager;

	public function __construct(protected App $kirby, ?ScanWorkerManager $workerManager = null) {
		$this->workerManager = $workerManager ?? new ScanWorkerManager($kirby, dirname(__DIR__));
	}

	public function start(): array {
		try {
			$this->requireUser();

			$store = new ScanStore($this->kirby);
			$current = $store->current();

			if (($current['isRunning'] ?? false) === true) {
				return $this->response($store);
			}

			$scanner = new Scanner($this->kirby);
			$scan = [
				'id' => bin2hex(random_bytes(16)),
				'isRunning' => true,
				'isComplete' => false,
				'cancelRequested' => false,
				'workerPid' => null,
				'startedAt' => date(DATE_ATOM),
				'finishedAt' => null,
				'stoppedAt' => null,
				'workerStartedAt' => null,
				'processedPages' => 0,
				'totalPages' => count($scanner->getPageQueue()),
				'currentPageTitle' => null,
				'lastError' => null,
			];

			$store->saveCurrent($scan);
			$this->workerManager->startWorker($scan['id']);
			$this->workerManager->awaitWorkerStart($store, $scan['id']);

			return $this->response($store);
		} catch (Throwable $exception) {
			$this->throwApiException($exception);
		}
	}

	public function stop(): array {
		try {
			$this->requireUser();

			$store = new ScanStore($this->kirby);
			$current = $store->current();

			if (($current['isRunning'] ?? false) === true) {
				$current['cancelRequested'] = true;
				$terminated = $this->workerManager->terminateWorker((int)($current['workerPid'] ?? 0));

				if ($terminated === true) {
					$current['isRunning'] = false;
					$current['isComplete'] = false;
					$current['cancelRequested'] = false;
					$current['workerPid'] = null;
					$current['currentPageTitle'] = null;
					$current['stoppedAt'] = date(DATE_ATOM);
					$current['lastError'] = null;
				}

				$store->saveCurrent($current);
			}

			return $this->response($store);
		} catch (Throwable $exception) {
			$this->throwApiException($exception);
		}
	}

	public function complete(): array {
		try {
			$this->requireUser();

			$store = new ScanStore($this->kirby);
			$data = $this->kirby->request()->data();
			$store->completeResult([
				'url' => $data['url'] ?? null,
				'pageTitle' => $data['pageTitle'] ?? null,
				'panelUrl' => $data['panelUrl'] ?? null,
				'reason' => $data['reason'] ?? null,
			]);

			return $this->response($store);
		} catch (Throwable $exception) {
			$this->throwApiException($exception);
		}
	}

	public function status(): array {
		try {
			$this->requireUser();

			return $this->response(new ScanStore($this->kirby));
		} catch (Throwable $exception) {
			$this->throwApiException($exception);
		}
	}

	protected function requireUser(): void {
		if ($this->kirby->user() === null) {
			throw new Exception('Unauthorized', 401);
		}
	}

	protected function response(ScanStore $store): array {
		return [
			'current' => $store->current(),
			'latest' => $store->latest(),
		];
	}

	protected function throwApiException(Throwable $exception): never {
		$code = (int)$exception->getCode();
		$code = $code >= 400 && $code < 600 ? $code : 500;

		throw new Exception($exception->getMessage(), $code, $exception);
	}
}
