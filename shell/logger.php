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
/**
 * Shell script; see usageHelp for options
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_Logger_Shell extends Mage_Shell_Abstract
{
    /**
     * Run shell script
     */
    public function run()
    {
        if ($this->getArg('clean')) {
            $days = $this->getArg('days');
            if (!$days) {
                $days = Mage::helper('firegento_logger')->getMaxDaysToKeep();
            }

            $deleted = Mage::getResourceSingleton('firegento_logger/db_entry')->cleanLogs($days);

            echo "Database log cleaned: kept $days days, deleted $deleted records." . PHP_EOL;
        } elseif ($this->getArg('rotate')) {
            Mage::getSingleton('firegento_logger/observer')->rotateLogs();
            echo "Rotation of log files finished.".PHP_EOL;
        } else {
            echo $this->usageHelp();
        }
    }

    /**
     * Retrieve Usage Help Message
     *
     * @return string
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f log.php -- [options]
        php -f log.php -- clean --days 1

  clean             Clean Logs
  --days <days>     Save log, days. (Minimum 1 day, if defined - ignoring system value)
  rotate            Rotate every file in var/log with ends with .log
  help              This help

USAGE;
    }
}

$shell = new Firegento_Logger_Shell();
$shell->run();
