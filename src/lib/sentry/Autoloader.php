<?php
/**
 * This file is part of a FireGento e.V. module.
 *
 * This FireGento e.V. module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * PHP version 5
 *
 * @category  FireGento
 * @package   FireGento_Logger
 * @author    FireGento Team <team@firegento.com>
 * @author    Wilfried Wolf <wilfried.wolf@sandstein.de>
 * @copyright 2022 FireGento Team (http://www.firegento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
class Sentry_Autoloader
{
    private static $prefixLengthsPsr4 = null;
    
    private static $prefixDirsPsr4 = null;

    private static $fallbackDirsPsr4 = null;

    private static $autoloadFiles = null;

    private static $classMap = null;


    /**
     * Registers an autoloader for sentry as composer replacement
     */
    public static function register()
    {
        spl_autoload_register(['Sentry_Autoloader', 'autoload'], true, true);

        if (self::$classMap === null) {
            self::$classMap = require __DIR__ . '/classmap.php';
        }

        if(self::$autoloadFiles === null) {
            self::$autoloadFiles = require __DIR__ . '/autoload_files.php';
        }

        if (self::$fallbackDirsPsr4 === null) {
            self::$fallbackDirsPsr4 = require __DIR__ . '/fallback_dirs_psr4.php';
        }

        if (self::$prefixLengthsPsr4 === null) {
            self::$prefixLengthsPsr4 = require __DIR__ . '/prefix_lengths_psr4.php';
        }

        if (self::$prefixDirsPsr4 == null) {
            self::$prefixDirsPsr4 = require __DIR__ . '/prefix_dirs_psr4.php';
        }

        self::autoloadFiles();
    }

    public static function autoload($class)
    {
        if ($file = self::findFile($class)) {
            include_file($file);
        }
    }

    private static function autoloadFiles()
    {
        foreach (self::$autoloadFiles as $file) {
            require_once $file;
        }
    }

    private static function findFile($class)
    {
        if (isset(self::$classMap[$class])) {
            return self::$classMap[$class];
        }

        if ($file = self::findFileWithExtension($class)) {
            return $file;
        }
    }

    private static function findFileWithExtension($class)
    {
        // PSR-4 lookup
        $logicalPathPsr4 = strtr($class, '\\', DIRECTORY_SEPARATOR) . '.php';

        $first = $class[0];
        if (isset(self::$prefixLengthsPsr4[$first])) {
            $subPath = $class;
            while (false !== $lastPos = strrpos($subPath, '\\')) {
                $subPath = substr($subPath, 0, $lastPos);
                $search = $subPath . '\\';
                if (isset(self::$prefixDirsPsr4[$search])) {
                    $pathEnd = DIRECTORY_SEPARATOR . substr($logicalPathPsr4, $lastPos + 1);
                    foreach (self::$prefixDirsPsr4[$search] as $dir) {
                        if (file_exists($file = $dir . $pathEnd)) {
                            return $file;
                        }
                    }
                }
            }
        }

        // PSR-4 fallback dirs
        foreach (self::$fallbackDirsPsr4 as $dir) {
            if (file_exists($file = $dir . DIRECTORY_SEPARATOR . $logicalPathPsr4)) {
                return $file;
            }
        }
        return false;
    }
}

/**
 * Scope isolated include.
 *
 * Prevents access to $this/self from included files.
 */
function include_file($file)
{
    include $file;
}
