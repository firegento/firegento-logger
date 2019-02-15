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
 * Advanced Formatted Logger
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Formatter_Advanced extends Zend_Log_Formatter_Simple
{

    protected $_maxDataLength;
    protected $_prettyPrint;

    /**
     * Class constructor
     *
     * @param null|string $format Logging Format
     */
    public function __construct($format = null)
    {
        $configFormat = Mage::helper('firegento_logger')->getLoggerConfig('general/format');
        if ($configFormat) {
            $format = str_replace('\n', PHP_EOL, $configFormat);
        }
        if (!$format) {
            $format = self::DEFAULT_FORMAT;
        }

        $this->_maxDataLength = Mage::helper('firegento_logger')->getLoggerConfig('general/max_data_length') ?: 1000;
        $this->_prettyPrint = Mage::helper('firegento_logger')->getLoggerConfig('general/pretty_print') && defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0;

        parent::__construct($format . PHP_EOL);
    }

    /**
     * Formats data into a single line to be written by the writer.
     *
     * @param  FireGento_Logger_Model_Event $event           Event Data
     * @return string formatted line to write to the log
     */
    public function format($event)
    {
        Mage::helper('firegento_logger')->addEventMetadata($event, '-', TRUE);

        $output = preg_replace_callback('/%(\w+)%/', function ($match) use ($event) {
            $value = isset($event[$match[1]]) ? $event[$match[1]] : '-';
            if (is_bool($value)) {
                return $value ? 'TRUE' : 'FALSE';
            } else if (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
                return "$value";
            } else if (is_array($value)) {
                return substr(@json_encode($value, $this->_prettyPrint), 0, $this->_maxDataLength);
            } else if (is_scalar($value)) {
                return "$value";
            } else {
                return gettype($value);
            }
        }, $this->_format);
        return $output;
    }

}
