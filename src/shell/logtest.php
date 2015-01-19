#!/usr/bin/env php
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
 * @copyright 2013 FireGento Team (http://www.firegento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
require_once 'abstract.php';
class FireGento_Shell_LogTest extends Mage_Shell_Abstract
{
    public function run()
    {
        $message = $this->getArg('message');
        if (!($message && is_string($message))) {
            $message = $this->getArg('m');
        }
        if (!($message && is_string($message))) {
            echo $this->usageHelp();
            return 1;
        }
        Mage::log($message);
        return 0;
    }
    /**
     * Retrieve Usage Help Message
     */
    public function usageHelp()
    {
        $script = __FILE__;
        return <<<USAGE
Usage:  php -f $script -- [options]

  -m|--message  The string to log to the configured loggers
  -h            Short alias for help
  help          This help

USAGE;
    }
}
$s = new FireGento_Shell_LogTest;
$s->run();
