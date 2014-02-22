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
set_include_path(get_include_path() . PATH_SEPARATOR . 'lib' . DS . 'raven-php' . DS . 'lib' . DS);
/**
 * Remote Sentry writer. Sends the Log Messages to a remote Sentry server.
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Sentry extends Zend_Log_Writer_Abstract
{
    /**
     * @var array
     */
    protected $_options = array();
    /**
     * sentry client
     *
     * @var Raven_Client
     */
    protected $_sentryClient;
    protected $_priorityToLevelMapping
        = array(
            0 => 'fatal',
            1 => 'fatal',
            2 => 'fatal',
            3 => 'error',
            4 => 'warning',
            5 => 'info',
            6 => 'info',
            7 => 'debug'
        );

    /**
     * ignore filename - it is Zend_Log_Writer_Abstract dependency
     *
     * @param string $filename pseudo filename to match
     *
     */
    public function __construct($filename)
    {
        /* @var $helper FireGento_Logger_Helper_Data */
        $helper = Mage::helper('firegento_logger');
        $options = array(
            'logger' => $helper->getLoggerConfig('sentry/logger_name')
        );
        $this->_sentryClient = new Raven_Client($helper->getLoggerConfig('sentry/apikey'), $options);
    }

    /**
     * Satisfy newer Zend Framework
     *
     * @param  array|Zend_Config $config Configuration
     * @return void|Zend_Log_Writer_Mock
     */
    static public function factory($config)
    {
    }

    /**
     * Places event line into array of lines to be used as message body.
     *
     * @param  array $event event data
     *
     * @throws Zend_Log_Exception
     * @return void
     */
    protected function _write($event)
    {
        try {
            /* @var $helper FireGento_Logger_Helper_Data */
            $helper = Mage::helper('firegento_logger');
            $helper->addEventMetadata($event);

            $additional = array(

                'file' => $event['file'],
                'line' => $event['line'],
            );

            foreach (array('REQUEST_METHOD', 'REQUEST_URI', 'REMOTE_IP', 'HTTP_USER_AGENT') as $key) {
                if (!empty($event[$key])) {
                    $additional[$key] = $event[$key];
                }
            }

            $this->_sentryClient->captureMessage(
                $event['message'], array(), $this->_priorityToLevelMapping[$event['priority']], false, $additional
            );

        } catch (Exception $e) {
            throw new Zend_Log_Exception($e->getMessage(), $e->getCode());
        }
    }
}
