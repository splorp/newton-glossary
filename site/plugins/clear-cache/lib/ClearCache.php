<?php

namespace ClickToNext;

use Kirby\Exception\Exception;
use Kirby\Filesystem\Dir;
use Kirby\Filesystem\F;

class ClearCache
{
    /**
     * @var array
     */
    public static $props;

    public static function relativePath(string $root, string $dir)
    {
        return str_replace(kirby()->root('index'), '', kirby()->root($root)) . '/' . $dir;
    }

    /**
     * @return array
     */
    public static function props()
    {
        if (static::$props !== null) {
            return static::$props;
        }

        return static::$props = [
            'cache' => ClearCache::cacheDirs(),
            'media' => ClearCache::mediaDirs(),
            'other' => ClearCache::otherDirs(),
        ];
    }

    /**
     * @return array
     */
    public static function cacheDirs()
    {
        $dirs = [];

        foreach (Dir::dirs(kirby()->root('cache'), null, true) as $dir) {
            $basename = basename($dir);

            $dirs[$basename] = [
                'name' => $basename,
                'path' => $dir,
                'text' => $basename,
                'info' => static::relativePath('cache', $basename)
            ];
        }

        return $dirs;
    }

    /**
     * @return array
     */
    public static function mediaDirs()
    {
        $dirs = [];

        foreach (Dir::dirs(kirby()->root('media'), null, true) as $dir) {
            $basename = basename($dir);

            $dirs[$basename] = [
                'name' => $basename,
                'path' => $dir,
                'text' => $basename,
                'info' => static::relativePath('media', $basename)
            ];
        }

        return $dirs;
    }

    /**
     * @return array[]
     */
    public static function otherDirs()
    {
        $dirs = [];

        // lock
        $locksPath = kirby()->root('content');
        if (static::lockFiles($locksPath)) {
            $dirs['lock'] = [
                'name' => 'lock',
                'path' => $locksPath,
                'text' => 'Content lock files',
                'info' => static::relativePath('content', '*.lock')
            ];
        }

        // logins
        $loginsFile = kirby()->root('accounts') . '/.logins';
        if (F::exists($loginsFile) === true) {
            $dirs['logins'] = [
                'name' => 'logins',
                'path' => $loginsFile,
                'text' => 'Users login data',
                'info' => static::relativePath('accounts', '.logins')
            ];
        }

        return $dirs;
    }

    /**
     * @param string $type
     * @return void
     * @throws Exception
     */
    public static function clearType(string $type): void
    {
        foreach (static::props()[$type] ?? [] as $dir) {
            static::clearDir($type, $dir);
        }
    }

    /**
     * @param string $type
     * @param string|array $dir
     * @return void
     * @throws Exception
     */
    public static function clearDir(string $type, $dir): void
    {
        if (is_array($dir) === false) {
            $dir = static::props()[$type][$dir] ?? null;

            if (empty($dir) === true) {
                throw new Exception('Invalid directory!');
            }
        }

        switch ($type) {
            case 'cache':
            case 'media':
                Dir::remove($dir['path']);
                break;
            case 'other':
                switch ($dir['name']) {
                    case 'logins':
                        F::remove($dir['path']);
                        break;
                    case 'lock':
                        static::clearLock($dir['path']);
                        break;
                }
                break;
        }
    }

    /**
     * @param string $path
     * @return void
     */
    public static function clearLock(string $path): void
    {
        $files = static::lockFiles($path);

        foreach ($files as $file) {
            if (is_file($file) === true) {
                F::remove($file);
            }
        }
    }

    /**
     * @param string $path
     * @param array $files
     * @return array
     */
    public static function lockFiles(string $path, array $files = []): array
    {
        foreach (Dir::read($path, null, true) as $dir) {
            if (is_dir($dir) === true) {
                $files = static::lockFiles($dir, $files);
            } else {
                if (basename($dir) === '.lock' && is_file($dir) === true) {
                    $files[] = $dir;
                }
            }
        }

        return $files;
    }
}
