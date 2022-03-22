<?php
/**
 * @author        Wilfried Wolf <wilfried.wolf@sandstein.de>
 * @copyright     Copyright © 2022 Sandstein Neue Medien GmbH (https://www.sandstein.de)
 */
class Sentry_Autoloader
{

    private static $prefixLengthsPsr4 = [ 
    'S' =>
        [
            'Symfony\\Polyfill\\Uuid\\' => 22,
            'Symfony\\Polyfill\\Php80\\' => 23,
            'Symfony\\Polyfill\\Php73\\' => 23,
            'Symfony\\Contracts\\Service\\' => 26,
            'Symfony\\Contracts\\HttpClient\\' => 29,
            'Symfony\\Component\\OptionsResolver\\' => 34,
            'Symfony\\Component\\HttpClient\\' => 29,
            'Sentry\\' => 7
        ],
    'P' =>
        [
            'Psr\\Log\\' => 8,
            'Psr\\Http\\Message\\' => 17,
            'Psr\\Http\\Client\\' => 16,
            'Psr\\Container\\' => 14,
        ],
    'J' =>
        [
            'Jean85\\' => 7,
        ],
    'H' =>
        [
            'Http\\Promise\\' => 13,
            'Http\\Message\\' => 13,
            'Http\\Factory\\Guzzle\\' => 20,
            'Http\\Discovery\\' => 15,
            'Http\\Client\\Common\\' => 19,
            'Http\\Client\\' => 12,
        ],
    'G' =>
        [
            'GuzzleHttp\\Psr7\\' => 16,
            'GuzzleHttp\\Promise\\' => 19,
        ],
    'C' =>
        [
            'Clue\\StreamFilter\\' => 18,
        ]
    ];
    
    private static $prefixDirsPsr4 = [
        'Symfony\\Polyfill\\Uuid\\' =>
            [
                0 => __DIR__ . '/symfony/polyfill-uuid',
            ],
        'Symfony\\Polyfill\\Php80\\' =>
            [
                0 => __DIR__ . '/symfony/polyfill-php80',
            ],
        'Symfony\\Polyfill\\Php73\\' =>
            [
                0 => __DIR__ . '/symfony/polyfill-php73',
            ],
        'Symfony\\Contracts\\Service\\' =>
            [
                0 => __DIR__ . '/symfony/service-contracts',
            ],
        'Symfony\\Contracts\\HttpClient\\' =>
            [
                0 => __DIR__ . '/symfony/http-client-contracts',
            ],
        'Symfony\\Component\\OptionsResolver\\' =>
            [
                0 => __DIR__ . '/symfony/options-resolver',
            ],
        'Symfony\\Component\\HttpClient\\' =>
            [
                0 => __DIR__ . '/symfony/http-client',
            ],
        'Sentry\\' =>
            [
                0 => __DIR__ . '/sentry/sentry/src',
            ],
        'Psr\\Log\\' =>
            [
                0 => __DIR__ . '/psr/log/Psr/Log',
            ],
        'Psr\\Http\\Message\\' =>
            [
                0 => __DIR__ . '/psr/http-message/src',
                1 => __DIR__ . '/psr/http-factory/src',
            ],
        'Psr\\Http\\Client\\' =>
            [
                0 => __DIR__ . '/psr/http-client/src',
            ],
        'Psr\\Container\\' =>
            [
                0 => __DIR__ . '/psr/container/src',
            ],
        'Jean85\\' =>
            [
                0 => __DIR__ . '/jean85/pretty-package-versions/src',
            ],
        'Http\\Promise\\' =>
            [
                0 => __DIR__ . '/php-http/promise/src',
            ],
        'Http\\Message\\' =>
            [
                0 => __DIR__ . '/php-http/message-factory/src',
                1 => __DIR__ . '/php-http/message/src',
            ],
        'Http\\Factory\\Guzzle\\' =>
            [
                0 => __DIR__ . '/http-interop/http-factory-guzzle/src',
            ],
        'Http\\Discovery\\' =>
            [
                0 => __DIR__ . '/php-http/discovery/src',
            ],
        'Http\\Client\\Common\\' =>
            [
                0 => __DIR__ . '/php-http/client-common/src',
            ],
        'Http\\Client\\' =>
            [
                0 => __DIR__ . '/php-http/httplug/src',
            ],
        'GuzzleHttp\\Psr7\\' =>
            [
                0 => __DIR__ . '/guzzlehttp/psr7/src',
            ],
        'GuzzleHttp\\Promise\\' =>
            [
                0 => __DIR__ . '/guzzlehttp/promises/src',
            ],
        'Clue\\StreamFilter\\' =>
            [
                0 => __DIR__ . '/clue/stream-filter/src',
            ]
    ];

    // not necessary here?
    private static $fallbackDirsPsr4 = [
        
    ];

    private static $autoloadFiles = [
        '/ralouphie/getallheaders/src/getallheaders.php',
        '/symfony/polyfill-php80/bootstrap.php',
        '/symfony/deprecation-contracts/function.php',
        '/symfony/polyfill-php73/bootstrap.php',
        '/clue/stream-filter/src/functions_include.php',
        '/php-http/message/src/filters.php',
        '/guzzlehttp/promises/src/functions_include.php',
        '/symfony/polyfill-uuid/bootstrap.php',
        '/sentry/sentry/src/functions.php'
    ];


    /**
     * Registers an autoloader for sentry as composer replacement
     */
    public static function register()
    {
        spl_autoload_register(['Sentry_Autoloader', 'autoload']);
        self::autoloadFiles();
    }

    public static function autoload($class)
    {
        if ($file = self::findFileWithExtension($class)) {
            require $file;
        }
    }

    private static function autoloadFiles()
    {
        foreach (self::$autoloadFiles as $file) {
            require __DIR__ .$file;
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