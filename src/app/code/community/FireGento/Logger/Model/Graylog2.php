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
require_once Mage::getBaseDir('lib') . DIRECTORY_SEPARATOR . 'Graylog2-gelf-php' . DIRECTORY_SEPARATOR . 'GELFMessage.php';
require_once Mage::getBaseDir('lib') . DIRECTORY_SEPARATOR . 'Graylog2-gelf-php' . DIRECTORY_SEPARATOR . 'GELFMessagePublisher.php';
/**
 * Model for Graylog logging
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Graylog2 extends Zend_Log_Writer_Abstract
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
     * @var bool
     */
    protected $_enableBacktrace = false;

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
            Mage::helper('firegento_logger')->addEventMetadata($event);

            $eofMessageFirstLine = strpos($event['message'], "\n");
            $shortMessage = (false === $eofMessageFirstLine) ? $event['message'] :
                substr($event['message'], 0, $eofMessageFirstLine);

            $msg = new GELFMessage();
            $msg->setTimestamp(microtime(true));
            $msg->setShortMessage($shortMessage);
            if ($event['backtrace']) {
                $msg->setFullMessage($event['message'] . "\n\nBacktrace:\n" . $event['backtrace']);
            } else {
                $msg->setFullMessage($event['message']);
            }
            $msg->setHost(gethostname());
            $msg->setLevel($event['priority']);
            $msg->setFacility($this->_options['app_name'] . $this->_options['filename']);
            $msg->setFile($event['file']);
            $msg->setLine($event['line']);
            $msg->setAdditional('store_code', $event['store_code']);
            $msg->setAdditional('time_elapsed', $event['time_elapsed']);
            $msg->setHost(php_uname('n'));
            foreach (array('REQUEST_METHOD', 'REQUEST_URI', 'REMOTE_IP', 'HTTP_USER_AGENT') as $key) {
                if (!empty($event[$key])) {
                    $msg->setAdditional($key, $event[$key]);
                }
            }

            $this->_publisher->publish($msg);
        } catch (Exception $e) {
            throw new Zend_Log_Exception($e->getMessage(), $e->getCode());
        }
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
