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

c::set('markdown.breaks', true);

/*

---------------------------------------
Plugin Configuration
---------------------------------------

*/

c::set('cachebuster', true);
c::set('plugin.ga.id', 'UA-3915509-13');
c::set('sitemap.include.invisible', true);

c::set('meta-tags.default', function(Page $page, Site $site) {
	return [
		'title' => site()->title() . ' — ' . $page->title(),
		'meta' => [
			'description' => $page->isHomePage()
				? $site->description()
				: ($page->description() != ''
					? $page->description()
					: excerpt($page->text(), 175)
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
					: excerpt($page->text(), 175)
				),
			'image' => site()->url() . '/assets/images/twitter-image-800x800.png'
		]
	];
});

/*

---------------------------------------
Debug Mode
---------------------------------------

*/

c::set('debug',false);
