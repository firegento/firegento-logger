<?php
/**
 * Created by JetBrains PhpStorm.
 * User: spies
 * Date: 31.03.12 (13 KW)
 * Time: 13:47
 * To change this template use File | Settings | File Templates.
 */
class Hackathon_Logger_Model_Mail extends Zend_Log_Writer_Mail
{
    private $transport = null;

    public function __construct($filename)
    {
        parent::__construct($this->getMail());
    }

    public function _write($event)
    {
        //Lazy intatiation of underlying mailer
        if ($this->_mail === null) {
            $this->_mail = $this->getMail();
        }
        parent::_write($event);
        /*
          $this->getMail()->setSubject('TestBetreff');
          $this->getMail()->setBodyText('Das ist der Text des Mails.');
          $this->getMail()->send($this->transport);
          */
    }

    public function getMail()
    {
        if ($this->_mail === null) {

            $this->_mail = new Zend_Mail();

            /** @var $helper Hackathon_Logger_Helper_Data */
            $helper = Mage::helper('hackathon_logger');

            $this->_mail->setFrom($helper->getLoggerConfig('mailcofig/from'), Mage::app()->getStore()->getName());
            $this->_mail->addTo($helper->getLoggerConfig('mailcofig/to'), 'Einige Empfänger');
        }
        return $this->_mail;
    }

    public function getTransport()
    {
        if ($this->transport === null) {
            /** @var $helper Hackathon_Logger_Helper_Data */
            $helper = Mage::helper('hackathon_logger');

            $config = array('auth' => 'login',
                'username' => $helper->getLoggerConfig('mailcofig/username'),
                'password' => $helper->getLoggerConfig('mailcofig/password'));

            $this->transport = new Zend_Mail_Transport_Smtp($helper->getLoggerConfig('mailcofig/hostname'), $config);
        }
        return $this->transport;
    }
}
