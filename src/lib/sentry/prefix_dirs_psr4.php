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
return [
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