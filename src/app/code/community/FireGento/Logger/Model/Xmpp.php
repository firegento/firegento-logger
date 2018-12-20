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
require_once 'XMPPHP/XMPP.php';
/**
 * Model for XMPP logging
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Xmpp extends FireGento_Logger_Model_Abstract
{
    /**
     * Array of formatted events to include in message body.
     *
     * @var array
     */
    protected $_eventsToSend = array();

    /**
     * Array of xmpp connection information. default to gtalk/gmail info
     *
     * @var array
     */
    public $options = array(
        'host' => '',
        'port' => 5222,
        'user' => '',
        'password' => '',
        'resource' => '',
        'server' => '',
        'recipient' => ''
    );

    /**
     * Class constructor
     *
     * @param  string $filename Filename
     * @return FireGento_Logger_Model_Xmpp XMPP instance
     */
    public function __construct($filename)
    {
        $this->setFormatter(new Zend_Log_Formatter_Simple());
        $helper = Mage::helper('firegento_logger');

        $this->options['host'] = $helper->getLoggerConfig('xmpp/host');
        $this->options['port'] = $helper->getLoggerConfig('xmpp/port');
        $this->options['user'] = $helper->getLoggerConfig('xmpp/username');
        $this->options['password'] = $helper->getLoggerConfig('xmpp/password');
        $this->options['resource'] = Mage::app()->getStore()->getName();
        $this->options['server'] = $helper->getLoggerConfig('xmpp/domain');
        $this->options['recipient'] = $helper->getLoggerConfig('xmpp/recipient');

        return $this;
    }

    /**
     * Places event line into array of lines to be used as message body.
     *
     * @param array $event Event data
     */
    protected function _write($event)
    {
        $event = Mage::helper('firegento_logger')->getEventObjectFromArray($event);
        $formattedEvent = $this->_formatter->format($event);
        $this->_eventsToSend[] = $formattedEvent;
    }

    /**
     * Sends message recipient if log entries are present.
     *
     * @throws Exception
     */
    public function shutdown()
    {
        // If there are events to send, use them as message body.
        // Otherwise, there is no message to be sent.
        if (empty($this->_eventsToSend)) {
            return;
        }

        // Finally, send the IM, but re-throw any exceptions at the
        // proper level of abstraction.
        try {
            $jabber = new XMPPHP_XMPP(
                $this->options['host'],
                $this->options['port'],
                $this->options['user'],
                $this->options['password'],
                $this->options['resource'],
                $this->options['server'],
                false,
                XMPPHP_Log::LEVEL_VERBOSE
            );


            try {
                $jabber->connect();
                $jabber->processUntil('session_start');
                $jabber->presence();
                $events = implode('', $this->_eventsToSend);
                $jabber->message($this->options['recipient'], $events);
                $jabber->disconnect();
            } catch (XMPPHP_Exception $e) {
                die($e->getMessage());
            }
        } catch (Exception $e) {
            throw new Zend_Log_Exception($e->getMessage(), $e->getCode());
        }
    }

}
