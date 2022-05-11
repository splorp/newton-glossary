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
	'sitemap.ignore' => [
		'error'
	],
	'routes' => [
		[
			'pattern' => 'sitemap.xml',
			'action'	=> function() {
					$pages = site()->pages()->index();

					// Fetch list of pages to ignore from the config file
					// If nothing is set in teh config file, ignore the error page
					$ignore = kirby()->option('sitemap.ignore', ['error']);

					$content = snippet('sitemap', compact('pages', 'ignore'), true);

					// return response with correct header type
					return new Kirby\Cms\Response($content, 'application/xml');
			}
		],
		[
			'pattern' => 'sitemap',
			'action'	=> function() {
				return go('sitemap.xml', 301);
			}
		],
		[
			'pattern' => 'sitemap.xsl',
			'method'  => 'GET',
			'action'  => function() {
				$stylesheet = f::read(kirby()->root('snippets') . '/sitemap.xsl');
				return new response($stylesheet, 'xsl');
			}
		],		
	],
	'schnti.cachebuster.active' => true,
	'splorp.paperback-export.includeUnlisted' => true,
	'splorp.paperback-export.includeChildren' => ['terms'],
	'splorp.paperback-export.excludeTemplate' => [],
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
