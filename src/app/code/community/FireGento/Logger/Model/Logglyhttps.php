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
define('LOGGER_CERTIFICATESFILE', Mage::getModuleDir('', 'FireGento_Logger') . '/extras/certificates/cacert.pem');
/**
 * Model for Loggly HTTPS logging
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Logglyhttps extends FireGento_Logger_Model_Abstract
{
    /**
     * @var string The URL of Loggly Log Server
     */
    protected $_logglyServer = 'logs-01.loggly.com';

    /**
     * @var int The port to use to communicate with Loggly Server.
     */
    protected $_logglyPort = 443;

    /**
     * @var string The Loggly path where to send Log Messages.
     */
    protected $_logglyPath = '/inputs';

    /**
     * @var string The SHA Input Key to be used to send Logs to Loggly via HTTPS
     */
    protected $_inputKey;

    /**
     * @var int The timeout to apply when sending data to Loggly servers, in seconds.
     */
    protected $_timeout = 5;

    /**
     * @var array Contains configuration options.
     */
    protected $_options = array();

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
        $this->_options['AppName'] = $helper->getLoggerConfig('logglyhttps/app_name');

        $this->_inputKey = $helper->getLoggerConfig('logglyhttps/inputkey');
        $this->_timeout = $helper->getLoggerConfig('logglyhttps/timeout');
    }

    /**
     * Builds a JSON Message that will be sent to a Loggly Server.
     *
     * @param  array $event           A Magento Log Event.
     * @param  bool  $enableBacktrace Indicates if a backtrace should be added to the log event.
     * @return string A JSON structure representing the message.
     */
    protected function BuildJSONMessage($event, $enableBacktrace = false)
    {
        /** @var $event FireGento_Logger_Model_Event */
        Mage::helper('firegento_logger')->addEventMetadata($event, null, $enableBacktrace);

        $fields = array();
        $fields['Level'] = $event->getPriority();
        $fields['FileName'] = $event->getFile();
        $fields['LineNumber'] = $event->getLine();
        $fields['StoreCode'] = $event->getStoreCode();
        $fields['Pid'] = getmypid();
        $fields['TimeElapsed'] = $event->getTimeElapsed();
        $fields['Host'] = php_uname('n');
        $fields['TimeStamp'] = date(DATE_ISO8601, strtotime($event->getTimestamp()));
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

        $curlHandler = curl_init(sprintf('https://%s/%s/%s/',
            $this->_logglyServer, $this->_logglyPath, $this->_inputKey));

        curl_setopt($curlHandler, CURLOPT_POST, 1);
        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, array(
            'User Agents: Vanilla Logger Plugin',
            'Content-Type: application/json',
            'Content-Length: '.strlen($message)
        ));
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $message);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandler, CURLOPT_TIMEOUT, (int) $this->_timeout);

        // Execute the request.
        $result = curl_exec($curlHandler);
        $succeeded  = curl_errno($curlHandler) == 0;
        $responseCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        $errorMsg = curl_error($curlHandler);

        // close cURL resource, and free up system resources
        curl_close($curlHandler);

        if (! ($succeeded && $responseCode == 200)) {
            throw new Zend_Log_Exception(
                sprintf('Error occurred posting log message to Loggly via HTTPS.
                CurlError: %s, ResponseCode: %s, Response: %s', $errorMsg, $responseCode, $result)
            );
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
        $event = Mage::helper('firegento_logger')->getEventObjectFromArray($event);
        $message = $this->BuildJSONMessage($event, $this->_enableBacktrace);
        return $this->PublishMessage($message);
    }

}
