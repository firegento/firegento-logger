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