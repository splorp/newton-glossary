<?php

/**
 * Kirby Paperback Export
 *
 * @version   2.0.1
 * @author    Grant Hutchinson <grant@splorp.com>
 * @copyright Grant Hutchinson <grant@splorp.com>
 * @link      https://github.com/splorp/kirby-paperback-export
 * @license   MIT
 */

Kirby::plugin('splorp/paperback-export', [
	'snippets' => [
		'paperback-export/content' => __DIR__ . '/snippets/content.php',
	],
	'routes' => [
		[
			'pattern' => 'export/paperback',
			'action' => function () {

				$includeUnlisted  = option('splorp.paperback-export.includeUnlisted', true);
				$includeChildren  = option('splorp.paperback-export.includeChildren', []);
				$excludeTemplate  = option('splorp.paperback-export.excludeTemplate', []);

				if (! is_array($includeChildren)) {
					throw new Exception('The option "splorp.paperback-export.includeChildren" must be an array.');
				}
				if (! is_array($excludeTemplate)) {
					throw new Exception('The option "splorp.paperback-export.excludeTemplate" must be an array.');
				}

				$languages   = site()->languages();
				$pages       = site()->index();
				$title       = site()->title();
				$description = site()->description();
				$version     = site()->version();
				$datestamp   = date('Y-M-d');
				$filename    = str::slug($title);

				/* Check whether to include unlisted pages */

				if (! $includeUnlisted) {
					$pages = $pages->listed();
				}

				/* Include only the children of specified pages */

				if ($includeChildren) {
					$pages = $pages->find($includeChildren)->children();
				}

				/* Exclude pages using specified templates */

				$pages = $pages->filterBy('intendedTemplate', 'not in', $excludeTemplate);

				$template = __DIR__ . '/snippets/export.php';
				$paperback  = tpl::load($template, compact('languages', 'pages', 'title', 'description', 'version', 'datestamp', 'filename'));

				return new response($paperback, 'txt');
			}
		]
	]
]);
