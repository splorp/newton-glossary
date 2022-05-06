<?php

/*

---------------------------------------
Kirby License
---------------------------------------

http://getkirby.com/license

*/

@include 'license.php';

/*

---------------------------------------
Kirby Configuration
---------------------------------------

http://getkirby.com/docs/advanced/options

*/

return [
	'debug' => false,
	'cache' => [
		'pages' => [
			'active' => true
		]
	],
	'markdown' => [
		'breaks' => true
	],
		],
		],
	'schnti.cachebuster.active' => true,
	'pedroborges.meta-tags.default' => function ($page, $site) {
		return [
			'title' => $site->title() . ' — ' . $page->title(),
			'meta' => [
				'description' => $page->isHomePage()
					? $site->description()
					: ($page->description() != ''
						? $page->description()
						: $page->text()->excerpt(175)
					),
				'keywords' => $site->keywords()
			],
			'link' => [
				'canonical' => $page->url()
			],
			'og' => [
				'title' => $page->title(),
				'type' => 'website',
				'site_name' => $site->title(),
				'url' => $page->url()
			],
			'twitter' => [
				'card' => 'summary',
				'site' => '@newtonglossary',
				'creator' => '@splorp',
				'url' => $page->url(),
				'title' => $page->title(),
				'description' => $page->isHomePage()
					? $site->description()
					: ($page->description() != ''
						? $page->description()
						: $page->text()->excerpt(175)
					),
				'image' => $site->url() . '/assets/meta/twitter-image-800x800.png'
			]
		];
	}
];
