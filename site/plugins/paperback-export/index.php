<?php
Kirby::plugin('splorp/paperback-export', [
	'snippets' => [
		'paperback-export/content' => __DIR__ . '/snippets/content.php',
	],
	'routes' => [
		[
			'pattern' => 'export/paperback',
			'action' => function () {

				$prefix = option('splorp.paperback-export.prefix', '');
				$fields = option('splorp.paperback-export.fields', []);
				$includeUnlisted = option('splorp.paperback-export.includeUnlisted', true);
				$includeChildren = option('splorp.paperback-export.includeChildren', []);
				$excludeTemplate = option('splorp.paperback-export.excludeTemplate', []);
				$includeDatestamp = option('splorp.paperback-export.includeDatestamp', false);

				if (! is_string($prefix)) {
					throw new Exception('The option “splorp.paperback-export.prefix” must be a string.');
				}
				if (! is_array($fields)) {
					throw new Exception('The option “splorp.paperback-export.fields” must be an array.');
				}
				if (! is_bool($includeUnlisted)) {
					throw new Exception('The option “splorp.paperback-export.includeUnlisted” must be a boolean.');
				}
				if (! is_array($includeChildren)) {
					throw new Exception('The option “splorp.paperback-export.includeChildren” must be an array.');
				}
				if (! is_array($excludeTemplate)) {
					throw new Exception('The option “splorp.paperback-export.excludeTemplate” must be an array.');
				}
				if (! is_bool($includeDatestamp)) {
					throw new Exception('The option “splorp.paperback-export.includeDatestamp” must be a boolean.');
				}

				$languages   = site()->languages();
				$pages       = site()->index();
				$title       = site()->title();
				$description = site()->description();
				$release     = site()->release();
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

				/* Check whether to include a datestamp */

				if ($includeDatestamp) { $datestamp = date('Y-M-d'); } else { $datestamp = ''; }

				/* Define template and variables */

				$template  = __DIR__ . '/snippets/export.php';
				$paperback = tpl::load($template, compact('languages', 'pages', 'title', 'description', 'prefix', 'fields', 'release', 'datestamp', 'filename'));

				return new response($paperback, 'txt');
			}
		]
	]
]);
