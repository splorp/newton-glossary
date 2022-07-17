<?php

use ClickToNext\ClearCache;
use Kirby\Cms\App as Kirby;
use Kirby\Exception\Exception;

load([
    'clicktonext\\clearcache' => __DIR__ . '/lib/ClearCache.php'
]);

Kirby::plugin('clicktonext/clear-cache', [
    'areas' => [
        'clear-cache' => function () {
            return [
                'label' => 'Clear cache',
                'icon' => 'trash',
                'menu' => true,
                'link' => 'clear-cache',
                'views' => [
                    [
                        'pattern' => 'clear-cache',
                        'action' => function () {
                            return [
                                'component' => 'clearcache',
                                'title' => 'Clear cache',
                                'props' => ClearCache::props(),
                            ];
                        }
                    ]
                ]
            ];
        }
    ],
    'api' => [
        'routes' => [
            [
                'pattern' => 'clear-cache',
                'method' => 'POST',
                'action' => function () {
                    $type = get('type');
                    $dir = get('dir');

                    if (empty($type) === true) {
                        throw new Exception('Invalid request!');
                    }

                    if (empty($dir) === true) {
                        ClearCache::clearType($type);
                    } else {
                        ClearCache::clearDir($type, $dir);
                    }

                    return true;
                }
            ]
        ]
    ]
]);
