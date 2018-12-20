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
require_once 'Graylog2-gelf-php/GELFMessage.php';
require_once 'Graylog2-gelf-php/GELFMessagePublisher.php';
/**
 * Model for Graylog logging
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Graylog2 extends FireGento_Logger_Model_Abstract
{
    /**
     * @var array
     */
    protected $_options = array();

    /**
     * @var GELFMessagePublisher
     */
    protected $_publisher;

    /**
     * @var GELFMessagePublisher[]
     */
    protected static $_publishers = array();

    /**
     * Use static method so all loggers share same publisher
     *
     * @param  string $hostname  Hostname
     * @param  int    $port      Port
     * @param  int    $chunkSize Chunk Size
     * @static
     * @return GELFMessagePublisher
     */
    protected static function getPublisher($hostname, $port, $chunkSize)
    {
        $key = "$hostname$port$chunkSize";
        if (!isset(self::$_publishers[$key])) {
            self::$_publishers[$key] = new GELFMessagePublisher($hostname, $port, $chunkSize);
        }

        return self::$_publishers[$key];
    }

    /**
     * Class constructor
     *
     * @param  string $filename Filename
     * @return FireGento_Logger_Model_Graylog2
     */
    public function __construct($filename)
    {
        /* @var $helper FireGento_Logger_Helper_Data */
        $helper = Mage::helper('firegento_logger');

        $this->_options['filename'] = basename($filename);
        $this->_options['app_name'] = $helper->getLoggerConfig('graylog2/app_name');
        $hostname = $helper->getLoggerConfig('graylog2/hostname');
        $port = $helper->getLoggerConfig('graylog2/port');
        $chunkSize = $helper->getLoggerConfig('graylog2/chunk_size');
        $this->_publisher = self::getPublisher($hostname, $port, $chunkSize);

        return $this;
    }

    /**
     * Places event line into array of lines to be used as message body.
     *
     * @param array $event Event data
     * @throws Zend_Log_Exception
     */
    protected function _write($event)
    {
        try {
            $event = Mage::helper('firegento_logger')->getEventObjectFromArray($event);

            Mage::helper('firegento_logger')->addEventMetadata($event);

            $message = trim($event->getMessage());

            $eofMessageFirstLine = strpos($message, "\n");
            $shortMessage = (false === $eofMessageFirstLine) ? $message :
                substr($message, 0, $eofMessageFirstLine);

            $msg = new GELFMessage();
            $msg->setTimestamp(microtime(true));
            $msg->setShortMessage($shortMessage);
            if ($event->getBacktrace()) {
                $msg->setFullMessage($message . "\n\nBacktrace:\n" . $event->getBacktrace());
            } else {
                $msg->setFullMessage($message);
            }
            $msg->setHost(gethostname());
            $msg->setLevel($event->getPriority());
            $msg->setFacility($this->_options['app_name'] . $this->_options['filename']);
            $msg->setFile($event->getFile());
            $msg->setLine($event->getLine());
            $msg->setAdditional('store_code', $event->getStoreCode());
            $msg->setAdditional('time_elapsed', $event->getTimeElapsed());
            $msg->setHost(php_uname('n'));
            foreach (array('getRequestMethod', 'getRequestUri', 'getRemoteIp', 'getHttpUserAgent','getHttpHost','getHttpCookie','getSessionData') as $method) {
                if (is_callable(array($event, $method)) && $event->$method()) {
                    $msg->setAdditional(lcfirst(substr($method, 3)), $event->$method());
                }
            }

            $this->_publisher->publish($msg);
        } catch (Exception $e) {
            throw new Zend_Log_Exception($e->getMessage(), $e->getCode());
        }
    }

}
