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
/**
 * Like ZLWStream but overrides the formatter to use the advanced formatter
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Stream extends Zend_Log_Writer_Stream
{
    /**
     * @var bool
     */
    protected $_enableBacktrace = false;

    /**
     * Setter for class variable _enableBacktrace
     *
     * @param bool $flag Flag for Backtrace
     */
    public function setEnableBacktrace($flag)
    {
        $this->_enableBacktrace = $flag;
    }

    /**
     * Write a message to the log.
     *
     * @param array $event Event Data
     * @throws Zend_Log_Exception
     */
    protected function _write($event)
    {
        $backtrace = debug_backtrace();
        array_shift($backtrace);
        array_shift($backtrace);
        array_shift($backtrace);
        $file = $backtrace[0]['file'];
        $moduleDir = $file;
        // The way this works is it sifts backwards through the log to find which module called this log.
        $codeStart = stripos($file, DS.'code'.DS);
        $moduleDir = substr($moduleDir, $codeStart +strlen(DS.'code'.DS));
        $moduleDir = str_ireplace('community' . DS, '', $moduleDir);
        $moduleDir = str_ireplace('local' . DS, '', $moduleDir);
        $endIndex = stripos($moduleDir, DS, stripos($moduleDir, DS)+1);
        $moduleKey = str_replace(DS, "_", substr($moduleDir, 0, $endIndex));
        if (!Mage::getSingleton('firegento_logger/manager')->isEnabled($moduleKey)) {
            return $this;
        }
        $event = Mage::helper('firegento_logger')->getEventObjectFromArray($event);

        $line = $this->_formatter->format($event, $this->_enableBacktrace);

        if (false === @fwrite($this->_stream, $line)) {
            //require_once 'Zend/Log/Exception.php';
            throw new Zend_Log_Exception("Unable to write to stream");
        }
    }

    /**
     * Satisfy newer Zend Framework
     *
     * @param  array|Zend_Config $config Configuration
     * @return void|Zend_Log_Writer_Mock
     */
    public static function factory($config)
    {

    }
}
