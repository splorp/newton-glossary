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

https://getkirby.com/docs/reference/system/options

*/

return [
	'debug' => false,
	'content.uuid' => false,
	'cache' => [
		'pages' => [
			'active' => true
		]
	],
	'kirbytext' => [
		'image' => [
			'figure' => false
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
			'pattern' => 'sitemap',
			'action' => function() {
				$pages = site()->pages()->index();

				// Fetch list of pages to ignore from the config file
				// If nothing is set in the config file, ignore the error page
				$ignore = kirby()->option('sitemap.ignore', ['error']);

				$content = snippet('sitemap', compact('pages', 'ignore'), true);

				// return response with correct header type
				return new Kirby\Cms\Response($content, 'application/xml');
			}
		],
		[
			'pattern' => 'sitemap.xml',
			'action' => function() {
				return go('sitemap', 301);
			}
		],
		[
			'pattern' => 'sitemap.xsl',
			'method' => 'GET',
			'action' => function() {
				$stylesheet = f::read(kirby()->root('snippets') . '/sitemap.xsl');
				return new response($stylesheet, 'xsl');
			}
		],
		[
			'pattern' => 'latest/feed',
			'action' => function() {
				$page = page('latest');
				$site = site();
				$pages = pages('terms', 'sources')->children()->filterBy('date', '!=', '')->sortBy(function ($pages) {
					return $pages->date()->toDate();
				}, 'desc')->limit(100);
				$content = snippet('latest', compact('site', 'page', 'pages'), true);

				// return response with correct header type
				return new Kirby\Cms\Response($content, 'application/xml');
			}
		],
		[
			'pattern' => 'latest.xml',
			'action' => function() {
				return go('latest/feed', 301);
			}
		],
	],
	'splorp.paperback-export.prefix' => '@@TOC ',
	'splorp.paperback-export.fields' => ['source' => 'related'],
	'splorp.paperback-export.includeUnlisted' => true,
	'splorp.paperback-export.includeChildren' => ['terms'],
	'splorp.paperback-export.excludeTemplate' => [],
	'splorp.paperback-export.includeDatestamp' => true,
	'pedroborges.meta-tags.default' => function ($page, $site) {
		return [
			'title' => $page->isHomePage()
					? $site->title() . ' — ' . $site->description()
					: $site->title() . ' — ' . $page->title(),
			'meta' => [
				'description' => $page->isHomePage()
					? $site->description()
					: ($page->description() != ''
						? $page->description()
						: $page->text()->excerpt(175)
					),
				'author' => $site->author(),
				'fediverse:creator' => $site->fediverse()
			],
			'link' => [
				'me' => $site->fediverse(),
				'canonical' => $page->url()
			],
			'og' => [
				'type' => $page->isHomePage()
					? 'website'
					: 'article',
				'article:author' => $site->fediverse(),
				'title' => $page->title(),
				'site_name' => $site->title(),
				'url' => $page->url(),
				'image' => $site->url() . '/assets/meta/og-image-800x800.png',
				'description' => $page->isHomePage()
					? $site->description()
					: ($page->description() != ''
						? $page->description()
						: $page->text()->excerpt(175)
					)
			]
		];
	}
];
