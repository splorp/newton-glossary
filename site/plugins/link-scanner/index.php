<?php

declare(strict_types=1);

use Kirby\Cms\App;
use ScottBoms\LinkScanner\PanelScanController;
use ScottBoms\LinkScanner\ScanStore;

load([
	'ScottBoms\\LinkScanner\\PanelScanController' => __DIR__ . '/lib/PanelScanController.php',
	'ScottBoms\\LinkScanner\\Scanner' => __DIR__ . '/lib/Scanner.php',
	'ScottBoms\\LinkScanner\\ScanStore' => __DIR__ . '/lib/ScanStore.php',
	'ScottBoms\\LinkScanner\\ScanWorkerManager' => __DIR__ . '/lib/ScanWorkerManager.php',
]);

if (
	version_compare(App::version() ?? '0.0.0', '5.0.0', '<') === true ||
	version_compare(App::version() ?? '0.0.0', '6.0.0', '>=') === true
) {
	throw new Exception('Link Scanner requires Kirby v5');
}

Kirby::plugin('scottboms/link-scanner', [
	'options' => [
		'timeout' => 8,
		'userAgent' => 'Kirby Link Scanner',
	],

	'areas' => [
		'link-scanner' => function () {
			if (kirby()->user() === null) {
				return [];
			}

			$store = new ScanStore(kirby());

			return [
				'label' => 'Link Scanner',
				'icon' => 'scanner',
				'breadcrumbLabel' => fn () => 'Link Scanner',
				'menu' => true,
				'link' => 'link-scanner',
				'views' => [
					[
						'pattern' => 'link-scanner',
						'action' => function () use ($store) {
							return [
								'component' => 'k-broken-links-view',
								'props' => [
								'initialLatest' => $store->latest(),
								'initialCurrent' => $store->current(),
								'startUrl' => kirby()->url('api') . '/link-scanner/start',
								'stopUrl' => kirby()->url('api') . '/link-scanner/stop',
								'completeUrl' => kirby()->url('api') . '/link-scanner/complete',
								'statusUrl' => kirby()->url('api') . '/link-scanner/status',
							],
						];
						},
					],
				],
			];
		},
	],

	'api' => [
		'routes' => [
			[
				'pattern' => 'link-scanner/start',
				'method' => 'POST',
				'action' => function () {
					return (new PanelScanController(kirby()))->start();
				},
			],
			[
				'pattern' => 'link-scanner/stop',
				'method' => 'POST',
				'action' => function () {
					return (new PanelScanController(kirby()))->stop();
				},
			],
			[
				'pattern' => 'link-scanner/complete',
				'method' => 'POST',
				'action' => function () {
					return (new PanelScanController(kirby()))->complete();
				},
			],
			[
				'pattern' => 'link-scanner/status',
				'method' => 'GET',
				'action' => function () {
					return (new PanelScanController(kirby()))->status();
				},
			],
		],
	],

	'info' => [
		'version' => '1.0.1',
		'homepage' => 'https://github.com/scottboms/kirby-link-scanner',
		'license' => 'MIT',
		'authors' => [[
			'name' => 'Scott Boms',
		]],
	],
]);
