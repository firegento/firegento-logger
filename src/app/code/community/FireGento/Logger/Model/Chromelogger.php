<?php
/**
 * This file is part of a FireGento e.V. module.
 * This FireGento e.V. module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
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
require_once 'chromelogger' . DS . 'ChromePHP.php';
/**
 * Model for Chrome logging
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Chromelogger extends FireGento_Logger_Model_Abstract
{

    /**
     * Write the data
     *
     * @param array $event Event Data
     */
    public function _write($event)
    {
        $event = Mage::helper('firegento_logger')->getEventObjectFromArray($event);

        $priority = $event->getPriority();
        $message  = $this->_formatter->format($event);

        if ($priority !== false) {
            switch ($priority)
            {
                case Zend_Log::EMERG:
                case Zend_Log::ALERT:
                case Zend_Log::CRIT:
                case Zend_Log::ERR:
                    ChromePhp::error($message);
                    break;
                case Zend_Log::WARN:
                    ChromePhp::warn($message);
                    break;
                case Zend_Log::NOTICE:
                case Zend_Log::INFO:
                case Zend_Log::DEBUG:
                    ChromePhp::info($message);
                    break;
                default:
                    Mage::log('Unknown loglevel at ' . __CLASS__);
                    break;
            }
        } else {
            Mage::log('Attached message event has no priority - skipping !');
        }
    }
}
