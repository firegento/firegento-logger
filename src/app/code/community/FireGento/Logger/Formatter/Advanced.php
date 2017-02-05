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
    /**
     * Default format
     */
    const DEFAULT_FORMAT = '%timestamp% %priorityName% (%priority%): %message%';

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

        parent::__construct($format . PHP_EOL);
    }

    /**
     * Formats data into a single line to be written by the writer.
     *
     * @param  FireGento_Logger_Model_Event $event           Event Data
     * @param  bool                         $enableBacktrace Backtrace Flag
     * @return string formatted line to write to the log
     */
    public function format($event, $enableBacktrace = false)
    {
        Mage::helper('firegento_logger')->addEventMetadata($event, '-', $enableBacktrace);

        if (substr(trim($this->_format), 0, 1) == '{') {
            return $this->_formatJson($event);
        }

        return parent::format($event->getEventDataArray());
    }

    /**
     * @param FireGento_Logger_Model_Event $event
     * @return string
     */
    protected function _formatJson($event)
    {
        $format = str_replace("\n", '', $this->_format);
        $format = str_replace(' ', '', $format);
        $json = json_decode($format, true);
        if ($json === null) {
            return '';
        }

        $eventData = $event->getEventDataArray();

        foreach ($json as $key => $jsonValue) {
            $eventDataKey = trim($jsonValue, '%');

            if (!array_key_exists($eventDataKey, $eventData)) {
                continue;
            }

            $value = $eventData[$eventDataKey];
            if ((is_object($value) && !method_exists($value, '__toString')) || is_array($value)) {
                $value = gettype($value);
            }

            $json[$key] = (string) $value;
        }

        $output = json_encode($json) . PHP_EOL;
        return $output;
    }
}
