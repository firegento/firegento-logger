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
require_once 'rsyslog/rsyslog.php';
/**
 * Model for Loggly Syslog logging
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Logglysyslog extends FireGento_Logger_Model_Rsyslog
{
    /**
     * @var int Default UDP Port for JSON Remote Syslog on Loggly
     */
    const DEFAULT_PORT = 42146;


    /**
     * Class constructor
     *
     * @param  string $filename Filename
     * @return FireGento_Logger_Model_Logglysyslog
     */
    public function __construct($filename)
    {
        /* @var $helper FireGento_Logger_Helper_Data */
        $helper = Mage::helper('firegento_logger');

        $this->_options['FileName'] = basename($filename);
        $this->_options['AppName'] = $helper->getLoggerConfig('logglysyslog/app_name');

        $this->_hostName = $helper->getLoggerConfig('logglysyslog/hostname');
        $this->_port = $helper->getLoggerConfig('logglysyslog/port');
        $this->_inputKey = $helper->getLoggerConfig('logglysyslog/inputkey');

        return $this;
    }


    /**
     * Transforms a Magento Log event into an associative array.
     *
     * @param  FireGento_Logger_Model_Event $event           A Magento Log Event.
     * @param  bool                         $enableBacktrace Indicates if a backtrace should be added to the log event.
     * @return array An associative array representation of the event.
     */
    protected function BuildJSONMessage( $event, $enableBacktrace = false)
    {
        Mage::helper('firegento_logger')->addEventMetadata($event, null, $enableBacktrace);

        $fields = array();
        $fields['Token'] = sprintf('[%s@41058]', $this->_inputKey);
        $fields['Level'] = $event->getPriority();
        $fields['FileName'] = $event->getFile();
        $fields['LineNumber'] = $event->getLine();
        $fields['StoreCode'] = $event->getStoreCode();
        $fields['Pid'] = getmypid();
        $fields['TimeElapsed'] = $event->getTimeElapsed();
        $fields['Host'] = php_uname('n');
        $fields['TimeStamp'] = date(DATE_RFC3339, strtotime($event->getTimestamp()));
        $fields['Facility'] = $this->_options['AppName'] . $this->_options['FileName'];
        $fields['Message'] = $event->getMessage();

        if ($event->getBacktrace()) {
            $fields['Backtrace'] = $event->getBacktrace();
        }

        foreach (array('getRequestMethod', 'getRequestUri', 'getRemoteIp', 'getHttpUserAgent','getHttpHost','getHttpCookie','getSessionData') as $method) {
            if (is_callable(array($event, $method)) && $event->$method()) {
                $fields[lcfirst(substr($method, 3))] = $event->$method();
            }
        }

        return $fields;
    }

    /**
     * Builds a Message that will be sent to a RSyslog Server.
     *
     * @param  FireGento_Logger_Model_Event $event A Magento Log Event.
     * @return string A string representing the message.
     */
    protected function buildSysLogMessage($event)
    {
        $message = $this->BuildJSONMessage($event, $this->_enableBacktrace);
        if (! is_string($message)) {
            $message = json_encode($message);
        }

        return new FireGento_Logger_Model_Loggly_LogglySyslogMessage (
            $message,
            self::DEFAULT_FACILITY,
            $event->getPriority(),
            strtotime($event->getTimestamp())
        );
    }
}
