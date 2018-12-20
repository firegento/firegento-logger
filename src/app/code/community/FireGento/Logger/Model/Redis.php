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
 * Model for Redis
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Redis extends FireGento_Logger_Model_Abstract
{
    protected $_redisServer = false;
    protected $_redisPort = false;
    protected $_redisKey = false;
    protected $_options = null;
    /**
     * @var int The timeout to apply when sending data to Redis servers, in seconds.
     */
    protected $_timeout = 5;

    /**
     * Class constructor
     *
     * @param string $filename Filename
     */
    public function __construct($filename)
    {
        /* @var $helper FireGento_Logger_Helper_Data */
        $helper = Mage::helper('firegento_logger');
        $this->_redisServer = $helper->getLoggerConfig('redis/host');
        $this->_redisPort = $helper->getLoggerConfig('redis/port');
        $this->_redisProtocol = $helper->getLoggerConfig('redis/protocol');
        $this->_redisKey = $helper->getLoggerConfig('redis/key');
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

    /**
     * Builds a JSON Message that will be sent to a Redis server.
     *
     * @param  FireGento_Logger_Model_Event $event           A Magento Log Event.
     * @param  bool                         $enableBacktrace Indicates if a backtrace should be added to the log event.
     * @return string A JSON structure representing the message.
     */
    protected function buildJSONMessage($event, $enableBacktrace = false)
    {
        Mage::helper('firegento_logger')->addEventMetadata($event, '-', $enableBacktrace);

        $fields = array();
        $fields['@timestamp'] = date('c', strtotime($event->getTimestamp()));
        $fields['@version'] = "1";
        $fields['level'] = $event->getPriority();
        $fields['file'] = $event->getFile();
        $fields['LineNumber'] = $event->getLine();
        $fields['StoreCode'] = $event->getStoreCode();
        $fields['TimeElapsed'] = $event->getTimeElapsed();
        $fields['source_host'] = $event->getHostname();
        $fields['message'] = $event->getMessage();

        return json_encode($fields);
    }

    /**
     * Sends a JSON Message to Redis.
     *
     * @param  string $message The JSON-Encoded Message to be sent.
     * @throws Zend_Log_Exception
     * @return bool True if message was sent correctly, False otherwise.
     */
    protected function publishMessage($message)
    {
        /* @var $helper FireGento_Logger_Helper_Data */
        $helper = Mage::helper('firegento_logger');

        $fp = fsockopen(
            $this->_redisServer,
            $this->_redisPort,
            $errorNumber,
            $errorMessage,
            $this->_timeout
        );
        $redisCommand = sprintf("PUBLISH %s '%s'\n", $this->_redisKey, addcslashes($message, "'"));
        try {
            $result = fwrite($fp, $redisCommand);
            fclose($fp);

            if ($result == false) {
                throw new Zend_Log_Exception(
                    sprintf($helper->__('Error occurred posting log message to redis via tcp. Posted Message: %s'),
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
        $event = Mage::helper('firegento_logger')->getEventObjectFromArray($event);
        $message = $this->buildJSONMessage($event, $this->_enableBacktrace);
        return $this->publishMessage($message);
    }
}
