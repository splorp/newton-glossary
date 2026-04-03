<?php

declare(strict_types=1);

namespace ScottBoms\LinkScanner;

use Exception;
use Kirby\Cms\App;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;
use Kirby\Http\Router;
use Kirby\Http\Url;
use Kirby\Toolkit\Str;

class Scanner {
	protected array $internalUrlCache = [];
	protected array $urlStatusCache = [];

	public function __construct(protected App $kirby) {
	}

	public function getPageQueue(): array {
		return $this->pageRecords();
	}

	public function scanAll(): array {
		$results = [];
		foreach ($this->pageRecords() as $entry) {
			$step = $this->scanRecord($entry);
			$results = [
				...$results,
				...$step['results'],
			];
		}
		return $this->buildSummary($results);
	}

	public function scanRecord(array $page): array {
		return $this->scanPageRecord($page);
	}

	public function buildSummary(array $results): array {
		usort($results, function (array $a, array $b): int {
			return [$a['pageTitle'], $a['url']] <=> [$b['pageTitle'], $b['url']];
		});

		return [
			'hasScanned' => true,
			'scannedAt' => date(DATE_ATOM),
			'totalBrokenLinks' => count($results),
			'totalCheckedLinks' => count($this->urlStatusCache),
			'results' => array_values($results),
		];
	}

	protected function scanPageRecord(array $page): array {
		$results = [];
		$seenPerPage = [];
		$pageLinks = $this->extractPageLinks($page);

		foreach ($pageLinks as $link) {
			$key = $page['id'] . '|' . $link;

			if (isset($seenPerPage[$key]) === true) {
				continue;
			}

			$seenPerPage[$key] = true;
			$status = $this->checkLink($link);

			if ($status['broken'] !== true) {
				continue;
			}

			$results[] = [
				'url' => $link,
				'pageTitle' => $page['title'],
				'panelUrl' => $page['panelUrl'],
				'reason' => $status['reason'],
			];
		}

		return [
			'results' => array_values($results),
		];
	}

	protected function extractPageLinks(array $page): array {
		$links = [];
		$extension = $this->kirby->contentExtension();

		foreach (Dir::files($page['root']) as $filename) {
			if (pathinfo($filename, PATHINFO_EXTENSION) !== $extension) {
				continue;
			}

			$content = F::read($page['root'] . '/' . $filename);
			if (is_string($content) !== true || trim($content) === '') {
				continue;
			}

			$links = array_merge($links, $this->extractLinks($content));
		}
		return array_values(array_unique($links));
	}

	protected function extractLinks(string $content): array {
		$matches = [];
		$matches = array_merge($matches, $this->extractMarkdownLinks($content));

		$patterns = [
			'/href=(["\'])(.*?)\1/i',
			'/\((?:link|url):\s*([^)\\s]+).*?\)/i',
			'/\b(https?:\/\/[^\s<>"\'\]]+)/i',
		];

		foreach ($patterns as $pattern) {
			preg_match_all($pattern, $content, $found);
			foreach ($found as $groupIndex => $group) {
				if ($groupIndex === 0) {
					continue;
				}

				foreach ($group as $value) {
					$normalized = $this->normalizeExtractedUrl($value);
					if ($normalized !== null) {
						$matches[] = $normalized;
					}
				}
			}
		}
		return array_values(array_unique($matches));
	}

	protected function extractMarkdownLinks(string $content): array
	{
		$matches = [];
		$pattern = '/\[[^\]]*]\(\s*(<[^>\n]+>|(?:https?:\/\/|\/|\.\.?\/)(?:[^()\s]+|\([^)\n]*\))+)(?:\s+(?:"[^"]*"|\'[^\']*\'|\([^)]+\)))?\s*\)/i';

		preg_match_all($pattern, $content, $found);

		foreach (($found[1] ?? []) as $value) {
			$normalized = $this->normalizeExtractedUrl($value);
			if ($normalized !== null) {
				$matches[] = $normalized;
			}
		}

		return $matches;
	}

	protected function normalizeExtractedUrl(string $url): ?string {
		$url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5));
		$url = trim($url, " \t\n\r\0\x0B\"'[]<>,");
		$url = $this->trimTrailingUrlPunctuation($url);

		if (
			$url === '' ||
			Str::startsWith($url, '#') === true ||
			Str::startsWith($url, 'mailto:') === true ||
			Str::startsWith($url, 'tel:') === true ||
			Str::startsWith($url, 'javascript:') === true ||
			Str::startsWith($url, 'data:') === true
		) {
			return null;
		}

		if (Str::startsWith($url, '//') === true) {
			$scheme = parse_url($this->kirby->site()->url(), PHP_URL_SCHEME) ?: 'https';
			return $scheme . ':' . $url;
		}

		if (preg_match('!^https?://!i', $url) === 1) {
			return $url;
		}

		if (Str::startsWith($url, '/') === true) {
			return $url;
		}

		if (
		Str::startsWith($url, './') === true ||
			Str::startsWith($url, '../') === true
		) {
			return '/' . ltrim($url, './');
		}
		return null;
	}

	protected function trimTrailingUrlPunctuation(string $url): string
	{
		$url = rtrim($url, ".,;:!?");

		while (
			Str::endsWith($url, ')') === true &&
			substr_count($url, ')') > substr_count($url, '(')
		) {
			$url = substr($url, 0, -1);
		}

		return $url;
	}

	protected function checkLink(string $url): array {
		if (isset($this->urlStatusCache[$url]) === true) {
			return $this->urlStatusCache[$url];
		}

		if ($this->isInternalUrl($url) === true) {
			return $this->urlStatusCache[$url] = $this->checkInternalLink($url);
		}

		$code = $this->checkExternalUrlWithCurl($url);

		if ($this->isSuccessfulCode($code) === true) {
			return $this->urlStatusCache[$url] = [
				'broken' => false,
				'reason' => null,
			];
		}

		return $this->urlStatusCache[$url] = [
			'broken' => $this->isBrokenCode($code) === true,
			'reason' => $this->isBrokenCode($code) === true ? 'HTTP ' . $code : null,
		];
	}

	protected function isSuccessfulCode(int $code): bool {
		return $code >= 200 && $code < 400;
	}

	protected function isBrokenCode(int $code): bool {
		return $code === 404 || $code >= 500;
	}

	protected function checkInternalLink(string $url): array {
		if (isset($this->internalUrlCache[$url]) === true) {
			return $this->internalUrlCache[$url];
		}

		$normalizedUrl = $this->normalizeComparableUrl($url);
		$publicPath = $this->internalPathFromUrl($url);

		if ($normalizedUrl === $this->normalizeComparableUrl($this->kirby->site()->url())) {
			return $this->internalUrlCache[$url] = [
				'broken' => false,
				'reason' => null,
			];
		}

		foreach ($this->pageRecords() as $page) {
			$pageUrl = $this->pageUrlFromId($page['id']);
			if ($pageUrl !== null && $normalizedUrl === $this->normalizeComparableUrl($pageUrl)) {
				return $this->internalUrlCache[$url] = [
					'broken' => false,
					'reason' => null,
				];
			}

			if ($publicPath !== null && $publicPath === $this->publicPathForPageId($page['id'])) {
				return $this->internalUrlCache[$url] = [
					'broken' => false,
					'reason' => null,
				];
			}
		}

		if ($publicPath !== null && $this->internalRouteExists($publicPath) === true) {
			return $this->internalUrlCache[$url] = [
				'broken' => false,
				'reason' => null,
			];
		}

		if ($publicPath !== null && F::exists($this->kirby->root('index') . $publicPath) === true) {
			return $this->internalUrlCache[$url] = [
				'broken' => false,
				'reason' => null,
			];
		}

		return $this->internalUrlCache[$url] = [
			'broken' => true,
			'reason' => 'Internal page or file not found',
		];
	}

	protected function internalPathFromUrl(string $url): ?string {
		$path = parse_url($url, PHP_URL_PATH);

		if (is_string($path) !== true) {
			return null;
		}

		$sitePath = parse_url($this->kirby->site()->url(), PHP_URL_PATH);
		$sitePath = is_string($sitePath) === true ? rtrim($sitePath, '/') : '';

		if ($sitePath !== '' && Str::startsWith($path, $sitePath) === true) {
			$path = substr($path, strlen($sitePath)) ?: '/';
		}
		return $path === '' ? '/' : $path;
	}

	protected function isInternalUrl(string $url): bool {
		if (
			Str::startsWith($url, '/') === true ||
			Str::startsWith($url, './') === true ||
			Str::startsWith($url, '../') === true
		) {
			return true;
		}

		$urlHost = parse_url($url, PHP_URL_HOST);
		$siteHost = parse_url($this->kirby->site()->url(), PHP_URL_HOST);

		if (is_string($urlHost) !== true || is_string($siteHost) !== true) {
			return false;
		}

		return strtolower($urlHost) === strtolower($siteHost);
	}

	protected function normalizeComparableUrl(string $url): string {
		$parts = parse_url($url);
		$scheme = strtolower((string)($parts['scheme'] ?? 'https'));
		$host = strtolower((string)($parts['host'] ?? ''));
		$path = (string)($parts['path'] ?? '/');

		if ($path !== '/') {
			$path = rtrim($path, '/');
		}

		return $scheme . '://' . $host . $path;
	}

	protected function pageUrlFromId(string $id): ?string {
		$page = $this->kirby->page($id);

		if ($page === null) {
			return null;
		}

		try {
			return $page->url();
		} catch (\Throwable) {
			return null;
		}
	}

	protected function pageRecords(): array {
		$records = [];
		$contentRoot = $this->kirby->root('content');

		foreach (Dir::index($contentRoot, true) as $relativePath) {
			$root = $contentRoot . '/' . $relativePath;

			if (is_dir($root) !== true) {
				continue;
			}

			$contentFiles = array_values(array_filter(
				Dir::files($root),
				fn (string $filename): bool => pathinfo($filename, PATHINFO_EXTENSION) === $this->kirby->contentExtension()
			));

			if ($contentFiles === []) {
				continue;
			}

			$id = $this->pageIdFromRelativePath($relativePath);

			if ($id === null) {
				continue;
			}

			$records[] = [
				'id' => $id,
				'root' => $root,
				'title' => $this->pageTitleFromDirectory($root, $id, $contentFiles),
				'panelUrl' => $this->panelUrlFromId($id),
			];
		}

		usort($records, fn (array $a, array $b): int => $a['id'] <=> $b['id']);
		return $records;
	}

	protected function pageIdFromRelativePath(string $relativePath): ?string {
		$parts = array_values(array_filter(explode('/', $relativePath), 'strlen'));
		$clean = [];

		foreach ($parts as $part) {
			if ($part === '_drafts') {
				continue;
			}
			$clean[] = preg_replace('/^\d+_/', '', $part);
		}

		$id = trim(implode('/', $clean), '/');
		return $id !== '' ? $id : null;
	}

	protected function pageTitleFromDirectory(string $root, string $id, array $contentFiles): string {
		$preferredFiles = array_values(array_filter(
			$contentFiles,
			fn (string $filename): bool => substr_count($filename, '.') === 1
		));

		$candidates = $preferredFiles !== [] ? $preferredFiles : $contentFiles;

		foreach ($candidates as $filename) {
			$content = (string)F::read($root . '/' . $filename);

			if (preg_match('/^Title:\s*(.+)$/mi', $content, $matches) === 1) {
				return trim($matches[1]);
			}
		}
		return ucwords(str_replace(['-', '_'], ' ', basename($id)));
	}

	protected function panelUrlFromId(string $id): string {
		$page = $this->kirby->page($id);

		if ($page !== null) {
			try {
				return Url::to($page->panel()->url());
			} catch (\Throwable) {
			}
		}

		return Url::to(
			'/' . trim($this->kirby->url('panel'), '/') . '/pages/' . str_replace('/', '+', trim($id, '/'))
		);
	}

	protected function publicPathForPageId(string $id): string {
		if ($id === 'home') {
			return '/';
		}
		return '/' . trim($id, '/');
	}

	protected function internalRouteExists(string $path): bool {
		try {
			$router = new Router($this->kirby->option('routes', []));
			$router->find(trim($path, '/'), 'GET');
			return true;
		} catch (Exception) {
			return false;
		}
	}

	protected function checkExternalUrlWithCurl(string $url): int {
		$timeout = (int)$this->kirby->option('scottboms.link-scanner.timeout', 8);
		$userAgent = (string)$this->kirby->option(
			'scottboms.link-scanner.userAgent',
			'Kirby Link Scanner'
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);

		$response = curl_exec($ch);

		if ($response === false) {
			return 0;
		}

		$code = (int)(curl_getinfo($ch, CURLINFO_HTTP_CODE) ?: 0);
		return $code;
	}
}
