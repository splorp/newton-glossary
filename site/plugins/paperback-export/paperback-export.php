<?php

/**
 * Kirby Paperback Export
 *
 * @version   1.0.1
 * @author    Grant Hutchinson <grant@splorp.com>
 * @copyright Grant Hutchinson <grant@splorp.com>
 * @link      https://github.com/splorp/kirby-paperback-export
 * @license   MIT
 */

kirby()->set('snippet', 'paperback.page', __DIR__ . '/snippets/page.php');

kirby()->set('route', [
    'pattern' => 'export/paperback',
    'action'  => function() {

        $includeChildren  = c::get('paperback.include.children', ['terms']);
        $excludeTemplate  = c::get('paperback.exclude.template', []);
        $includeInvisible = c::get('paperback.include.invisible', true);

        if (! is_array($includeChildren)) {
            throw new Exception('The option "paperback.include.children" must be an array.');
        }
        if (! is_array($excludeTemplate)) {
            throw new Exception('The option "paperback.exclude.template" must be an array.');
        }

        $languages   = site()->languages();
        $pages       = site()->index();
        $title       = site()->title();
        $description = site()->description();
        $version     = site()->version();
        $filename    = str::slug($title);

		/* Check whether to include invisible pages */

        if (! $includeInvisible) {
            $pages = $pages->visible();
        }

		/* Include only the children of the specified page */

        if ($includeChildren) {
            $pages = $pages->find($includeChildren)->children();
        }

		/* Exclude pages using certain templates */

        $pages = $pages->filterBy('intendedTemplate', 'not in', $excludeTemplate);

        $template = __DIR__ . DS . 'paperback-export.txt.php';
        $paperback  = tpl::load($template, compact('languages', 'pages', 'title', 'description', 'version', 'filename'));

        return new response($paperback, 'txt');
    }
]);
