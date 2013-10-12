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
require_once 'lib/rsyslog/rsyslog.php';
/**
 * Model for Loggly Syslog logging
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_Logger_Model_Logglysyslog extends Firegento_Logger_Model_Rsyslog
{
    /**
     * @var int Default UDP Port for JSON Remote Syslog on Loggly
     */
    const DEFAULT_PORT = 42146;

    /**
     * Transforms a Magento Log event into an associative array.
     *
     * @param  array $event           A Magento Log Event.
     * @param  bool  $enableBacktrace Indicates if a backtrace should be added to the log event.
     * @return array An associative array representation of the event.
     */
    protected function BuildJSONMessage($event, $enableBacktrace = false)
    {
        Mage::helper('firegento_logger')->addEventMetadata($event, '-', $enableBacktrace);

        $fields = array();
        $fields['Level'] = $event['priority'];
        $fields['FileName'] = $event['file'];
        $fields['LineNumber'] = $event['line'];
        $fields['StoreCode'] = $event['store_code'];
        $fields['TimeElapsed'] = $event['time_elapsed'];
        $fields['Host'] = php_uname('n');
        $fields['TimeStamp'] = date('Y-m-d H:i:s', strtotime($event['timestamp']));
        $fields['Facility'] = $this->_options['AppName'] . $this->_options['FileName'];

        if ($event['backtrace']) {
            $fields['Message'] = $event['message'] . "\n\nBacktrace:\n" . $event['backtrace'];
        } else {
            $fields['Message'] = $event['message'];
        }

        foreach (array('REQUEST_METHOD', 'REQUEST_URI', 'REMOTE_IP', 'HTTP_USER_AGENT') as $key) {
            if (!empty($event[$key])) {
                $fields[$key] = $event[$key];
            }
        }

        return $fields;
    }

    /**
     * Builds a Message that will be sent to a RSyslog Server.
     *
     * @param  array $event A Magento Log Event.
     * @return string A string representing the message.
     */
    protected function BuildSysLogMessage($event)
    {
        return new Firegento_Logger_Model_Loggly_LogglySyslogMessage(
            $this->BuildJSONMessage($event, $this->_enableBacktrace),
            self::DEFAULT_FACILITY,
            $event['priority'],
            strtotime($event['timestamp']));
    }

    /**
     * Class constructor
     *
     * @param  string $filename Filename
     * @return Firegento_Logger_Model_Logglysyslog
     */
    public function __construct($filename)
    {
        /* @var $helper Firegento_Logger_Helper_Data */
        $helper = Mage::helper('firegento_logger');

        $this->_options['FileName'] = basename($filename);
        $this->_options['AppName'] = $helper->getLoggerConfig('logglysyslog/app_name');

        $this->_hostName = $helper->getLoggerConfig('logglysyslog/hostname');
        $this->_port = $helper->getLoggerConfig('logglysyslog/port');
        $this->_timeout = $helper->getLoggerConfig('logglysyslog/timeout');

        return $this;
    }
}

