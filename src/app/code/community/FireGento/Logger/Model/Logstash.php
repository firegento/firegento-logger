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
 * Model for Logstash
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Logstash extends Zend_Log_Writer_Abstract
{
    /**
     * @var bool Indicates if backtrace should be added to the Log Message.
     */
    protected $_enableBacktrace = false;
    protected $_logstashServer = false;
    protected $_logstashPort = false;
    protected $_options = null;

    /**
     * @var int The timeout to apply when sending data to Loggly servers, in seconds.
     */
    protected $_timeout = 5;


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
     * Class constructor
     *
     * @param  string $filename Filename
     * @return FireGento_Logger_Model_Logglyhttps
     */
    public function __construct($filename)
    {
        /* @var $helper FireGento_Logger_Helper_Data */
        $helper = Mage::helper('firegento_logger');

        $this->_options['FileName'] = basename($filename);
        $this->_options['AppName'] = $helper->getLoggerConfig('logstash/app_name');
        $this->_logstashServer = $helper->getLoggerConfig('logstash/hostname');
        $this->_logstashPort = $helper->getLoggerConfig('logstash/port');

    }

    /**
     * Builds a JSON Message that will be sent to a Logstath Server.
     *
     * @param  array $event           A Magento Log Event.
     * @param  bool  $enableBacktrace Indicates if a backtrace should be added to the log event.
     * @return string A JSON structure representing the message.
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
            $fields['message'] = $event['message'] . "\n\nBacktrace:\n" . $event['backtrace'];
        } else {
            $fields['message'] = $event['message'];
        }

        foreach (array('REQUEST_METHOD', 'REQUEST_URI', 'REMOTE_IP', 'HTTP_USER_AGENT') as $key) {
            if (!empty($event[$key])) {
                $fields[$key] = $event[$key];
            }
        }

        return json_encode($fields);
    }

    /**
     * Sends a JSON Message to Loggly.
     *
     * @param  string $message The JSON-Encoded Message to be sent.
     * @throws Zend_Log_Exception
     * @return bool True if message was sent correctly, False otherwise.
     */
    protected function PublishMessage($message)
    {
        /* @var $helper FireGento_Logger_Helper_Data */
        $helper = Mage::helper('firegento_logger');

        $fp = fsockopen(
            sprintf('http://%s', $this->_logstashServer),
            $this->_logstashPort,
            $errorNumber,
            $errorMessage,
            $this->_timeout
        );

        // TODO Replace HTTPS with UDP
        try {
            $out = sprintf("POST %s/%s HTTP/1.1\r\n",
                $this->_logglyPath,
                $this->_inputKey
            );
            $out .= sprintf("Host: %s\r\n", $this->_logstashServer);
            $out .= "Content-Type: application/json\r\n";
            $out .= "User-Agent: Vanilla Logger Plugin\r\n";
            $out .= sprintf("Content-Length: %d\r\n", strlen($message));
            $out .= "Connection: Close\r\n\r\n";
            $out .= $message . "\r\n\r\n";

            $result = fwrite($fp, $out);
            fclose($fp);

            if ($result == false) {
                throw new Zend_Log_Exception(
                    sprintf($helper->__('Error occurred posting log message to Loggly via HTTPS. Posted Message: %s'),
                    $message)
                );
            }
        } catch (Exception $e) {
            throw new Zend_Log_Exception($e->getMessage(), $e->getCode());
        }

        return true;
    }

    /**
     * Places event line into array of lines to be used as message body.
     *
     * @param  array $event Event data
     * @return bool True if message was sent correctly, False otherwise.
     */
    protected function _write($event)
    {
        $message = $this->BuildJSONMessage($event, $this->_enableBacktrace);
        return $this->PublishMessage($message);
    }

    /**
     * Satisfy newer Zend Framework
     *
     * @param  array|Zend_Config $config Configuration
     * @return void|Zend_Log_FactoryInterface
     */
    public static function factory($config)
    {

    }
}
