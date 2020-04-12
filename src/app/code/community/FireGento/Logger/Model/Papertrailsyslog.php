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
 * Model for Papertrail Syslog logging
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Papertrailsyslog extends FireGento_Logger_Model_Rsyslog
{

    /**
     * Class constructor
     *
     * @param  string $filename Filename
     * @return FireGento_Logger_Model_Papertrailsyslog
     */
    public function __construct($filename)
    {
        /* @var $helper FireGento_Logger_Helper_Data */
        $helper = Mage::helper('firegento_logger');
        $this->_options['AppName'] = $helper->getLoggerConfig('papertrailsyslog/app_name');
        $this->_options['FileName'] = basename($filename);

        $this->_hostName = $helper->getLoggerConfig('papertrailsyslog/hostname');
        $this->_port = $helper->getLoggerConfig('papertrailsyslog/port');
        return $this;
    }

    /**
     * Transforms a Magento Log event into a string with meta information.
     *
     * @param  FireGento_Logger_Model_Event $event A Magento Log Event.
     * @param  bool $enableBacktrace Indicates if a backtrace should be added to the log event.
     * @return array An associative array representation of the event.
     */
    protected function BuildStringMessage( $event, $enableBacktrace = false)
    {
        Mage::helper('firegento_logger')->addEventMetadata($event, null, $enableBacktrace);
        $message = '[' . $event->getPriorityName() . ']';
        $message .= ' [' . $event->getStoreCode() . ']';
        foreach (array('getUserAgent', 'getRequestUri', 'getRequestData', 'getRemoteIp', 'getHttpUserAgent', 'getRemoteAddress') as $method) {
            if (is_callable(array($event, $method)) && $event->$method()) {
                $message .= '[' . substr($method, 3) . ':' . $event->$method() . '] ';
            }
        }
        $message .= ' ' . $event->getTimeElapsed() . '';
        $message .= ' ' . $event->getMessage();
        $message .= ($event->getBacktrace() ? ' ' . $event->getBacktrace() : ' ');
        return $message;
    }

    /**
     * Builds a Message that will be sent to the Papertrail Server.
     *
     * @param  FireGento_Logger_Model_Event $event A Magento Log Event.
     * @return string A string representing the message.
     */
    protected function buildSysLogMessage($event)
    {
        $message = $this->BuildStringMessage($event, $this->_enableBacktrace);

        return new FireGento_Logger_Model_Papertrail_PapertrailSyslogMessage(
            $message,
            16,
            $event->getPriority(),
            strtotime($event->getTimestamp()),
            [
                'HostName' => sprintf(
                    '%s %s/%s',
                    $event->getHostname(),
                    $this->_options['AppName'],
                    $this->_options['FileName']
                ),
                'FQDN' => null,
                'ProcessName' => $event->getFile() . ':' . $event->getLine()
            ]
        );
    }
}
