<?php
/**
 * User: spies
 * Date: 31.03.12 (13 KW)
 * Time: 13:47
 */
class Hackathon_Logger_Model_Mail extends Zend_Log_Writer_Mail
{
    private $transport = null;

    /**
     * @param String $filename
     */
    public function __construct($filename)
    {
        parent::__construct($this->getMail());
    }

    /**
     * @param $event
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
     * @return Zend_Mail
     */
    public function getMail()
    {
        if ($this->_mail === null) {

            $this->_mail = new Zend_Mail();

            /** @var $helper Hackathon_Logger_Helper_Data */
            $helper = Mage::helper('hackathon_logger');

            $this->_mail->setFrom($helper->getLoggerConfig('mailconfig/from'), Mage::app()->getStore()->getName());
            $this->_mail->addTo($helper->getLoggerConfig('mailconfig/to'));
            $this->_mail->setDefaultTransport($this->getTransport());
        }
        return $this->_mail;
    }

    /**
     * @return Zend_Mail_Transport_Abstract
     */
    public function getTransport()
    {
        if ($this->transport === null) {
            /** @var $helper Hackathon_Logger_Helper_Data */
            $helper = Mage::helper('hackathon_logger');

            $config = array('auth' => 'login',
                'username' => $helper->getLoggerConfig('mailconfig/username'),
                'password' => $helper->getLoggerConfig('mailconfig/password'));

            $this->transport = new Zend_Mail_Transport_Smtp($helper->getLoggerConfig('mailconfig/hostname'), $config);
        }
        return $this->transport;
    }

    /**
     * Satisfy newer Zend Framework
     *
     * @static
     * @param $config
     */
    static public function factory($config) {}

}
