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
 * Mail Logger
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Mail extends Zend_Log_Writer_Mail
{
    /**
     * @var Zend_Mail_Transport_Smtp|null
     */
    protected $_transport = null;

    /**
     * Instantiate the mail object
     *
     * @param string $filename Filename
     */
    public function __construct($filename)
    {
        parent::__construct($this->getMail());
    }

    /**
     * Send the log mails
     *
     * @param array $event Event data
     */
    public function _write($event)
    {
        //Lazy intatiation of underlying mailer
        if ($this->_mail === null) {
            $this->_mail = $this->getMail();
        }

        parent::_write($event);
    }

    /**
     * Get the mail object
     *
     * @return Zend_Mail
     */
    public function getMail()
    {
        if ($this->_mail === null) {
            $this->_mail = new Zend_Mail();

            /** @var $helper FireGento_Logger_Helper_Data */
            $helper = Mage::helper('firegento_logger');

            $storeName = Mage::app()->getStore()->getName();
            $subject = $storeName .' - Debug Information';

            $this->_mail->setFrom($helper->getLoggerConfig('mailconfig/from'), $storeName);
            $this->_mail->setSubject($subject);
            $this->_mail->addTo($helper->getLoggerConfig('mailconfig/to'));
            $this->_mail->setDefaultTransport($this->getTransport());
        }

        return $this->_mail;
    }

    /**
     * Retreive the transport object
     *
     * @return Zend_Mail_Transport_Abstract Transport Object
     */
    public function getTransport()
    {
        if ($this->_transport === null) {
            /** @var $helper FireGento_Logger_Helper_Data */
            $helper = Mage::helper('firegento_logger');

            $transportFlag = $helper->getLoggerConfig('mailconfig/sendmailtransport');

            switch ($transportFlag) {
                case "1": // use Sendmail transport
                    $transport = new Zend_Mail_Transport_Sendmail();
                    break;

                case "0": // use Smtp transport
                    // fall-through intended
                default:
                    $smtpConfig = array(
                        'auth'     => 'login',
                        'username' => $helper->getLoggerConfig('mailconfig/username'),
                        'password' => $helper->getLoggerConfig('mailconfig/password')
                    );

                    // Reset config array if username is empty
                    if (empty($smtpConfig['username'])) {
                        $smtpConfig = array();
                    }

                    $transport = new Zend_Mail_Transport_Smtp(
                        $helper->getLoggerConfig('mailconfig/hostname'), $smtpConfig
                    );
            }

            $this->_transport = $transport;
        }

        return $this->_transport;
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
