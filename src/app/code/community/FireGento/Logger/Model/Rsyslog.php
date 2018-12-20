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
 * Remote Syslog writer. Sends the Log Messages to a Remote Syslog server.
 * Messages are sent as plain text.
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Rsyslog extends FireGento_Logger_Model_Abstract
{
    /**
     * @var int The default Timeout to be used when communicating with the Remote Syslog Server.
     */
    const DEFAULT_TIMEOUT = 1;

    /**
     * @todo Allow User to choose the Facility from one of the values provided by SyslogFacility Class.
     *
     * @var int The default Facility used to build Syslog Messages.
     */
    const DEFAULT_FACILITY = SyslogFacility::USER;

    /**
     * @var RSyslog Writer Instance
     */
    protected $_syslogPublisher;

    /**
     * The properties below will be set automatically by Log4php with the data it will get from the configuration.
     *
     * @var string The address of the RSyslog log to which the log messages will be sent.
     */
    protected $_hostName;

    /**
     * @var int The port to use to connect to RSyslog server.
     */
    protected $_port;

    /**
     * @var int Timeout tro be used when communicating with Remote SysLog Server
     */
    protected $_timeout;

    /**
     * @var array Contains configuration options.
     */
    protected $_options = array();

    /**
     * Builds and returns the full URL where the Log messages will be sent.
     *
     * @return \RSyslog
     */
    protected function getSyslogPublisher()
    {
        if (empty($this->_syslogPublisher)) {
            $this->_syslogPublisher = new RSyslog(($this->_hostName . ':' . $this->_port), $this->_timeout);
        }

        return $this->_syslogPublisher;
    }

    /**
     * Builds a Message that will be sent to a RSyslog Server.
     *
     * @param  FireGento_Logger_Model_Event $event A Log4php Event.
     * @return SyslogMessage
     */
    protected function buildSysLogMessage($event)
    {
        $aUrlParts = parse_url(Mage::getBaseUrl());
        return new SyslogMessage(
            $this->_formatter->format($event, $this->_enableBacktrace),
            self::DEFAULT_FACILITY,
            $event->getPriority(),
            strtotime($event->getTimestamp()),
            array(
                'HostName'    => gethostname(),
                'FQDN'        => $aUrlParts['host'],
                'ProcessName' => $this->_options['AppName'],
            )
        );
    }

    /**
     * Sends a Message to a RSyslog server.
     *
     * @param  SyslogMessage $message The Message to be sent.
     * @throws Zend_Log_Exception
     * @return bool True if message was sent correctly, False otherwise.
     */
    protected function publishMessage(SyslogMessage $message)
    {
        $result = $this->GetSyslogPublisher()->Send($message);
        if ($result === true) {
            return true;
        }

        /* @var $helper FireGento_Logger_Helper_Data */
        $helper = Mage::helper('firegento_logger');

        // In case of error, RSysLog publisher returns an array containing an Error Number
        // and an Error Message
        throw new Zend_Log_Exception(
            sprintf(
                $helper->__('Error occurred sending log to Remote Syslog Server. Error number: %d. Error Message: %s'),
                $result[0],
                $result[1]
            )
        );
    }

    /**
     * Class constructor
     *
     * @param  string $filename Filename
     * @return FireGento_Logger_Model_Rsyslog Rsyslog instance
     */
    public function __construct($filename)
    {
        /* @var $helper FireGento_Logger_Helper_Data */
        $helper = Mage::helper('firegento_logger');

        $this->_options['FileName'] = basename($filename);
        $this->_options['AppName'] = $helper->getLoggerConfig('rsyslog/app_name');

        $this->_hostName = $helper->getLoggerConfig('rsyslog/hostname');
        $this->_port = $helper->getLoggerConfig('rsyslog/port');
        $this->_timeout = $helper->getLoggerConfig('rsyslog/timeout');

        return $this;
    }

    /**
     * Places event line into array of lines to be used as message body.
     *
     * @param  array $event Event data
     * @return bool Result of write
     */
    protected function _write($event)
    {
        $event = Mage::helper('firegento_logger')->getEventObjectFromArray($event);
        if(!$event->getTimestamp()) {
            $event->setTimestamp(now());
        }
        $message = $this->buildSysLogMessage($event);
        return $this->publishMessage($message);
    }

}
