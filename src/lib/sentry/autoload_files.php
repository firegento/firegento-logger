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
    __DIR__ .'/ralouphie/getallheaders/src/getallheaders.php',
    __DIR__ .'/symfony/polyfill-php80/bootstrap.php',
    __DIR__ .'/symfony/deprecation-contracts/function.php',
    __DIR__ .'/symfony/polyfill-php73/bootstrap.php',
    __DIR__ .'/clue/stream-filter/src/functions_include.php',
    __DIR__ .'/php-http/message/src/filters.php',
    __DIR__ .'/guzzlehttp/promises/src/functions_include.php',
    __DIR__ .'/symfony/polyfill-uuid/bootstrap.php',
    __DIR__ .'/sentry/sentry/src/functions.php'
];