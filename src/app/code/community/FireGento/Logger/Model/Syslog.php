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
 * @author    Lee Saferite <lee.saferite@aoe.com>
 * @copyright Lee Saferite <lee.saferite@aoe.com>
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
/**
 * Syslog Wrapper
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   Lee Saferite <lee.saferite@aoe.com>
 * @since    2014-07-01
 */
class FireGento_Logger_Model_Syslog extends Zend_Log_Writer_Syslog
{
    /**
     * @param string $filename
     */
    public function __construct($filename)
    {
        $logDir = Mage::getBaseDir('var') . DS . 'log' . DS;
        $filename = substr($filename, strlen($logDir));

        /* @var $helper FireGento_Logger_Helper_Data */
        $helper = Mage::helper('firegento_logger');

        $options = array(
            'application' => $helper->getLoggerConfig('syslog/application') . $filename,
            'facility'    => $helper->getLoggerConfig('syslog/facility'),
        );

        parent::__construct($options);
    }
}
