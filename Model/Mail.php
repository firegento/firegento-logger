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

	public function __construct($filename) {
		parent::__construct($this->getMail());
	}

	public function _write($event){
		//Lazy intatiation of underlying mailer
		if($this->_mail === null) {
			$this->_mail = $this->getMail();
		}
		parent::_write($event);
		/*
		$this->getMail()->setSubject('TestBetreff');
		$this->getMail()->setBodyText('Das ist der Text des Mails.');
		$this->getMail()->send($this->transport);
		*/
	}

	public function getMail(){
		if($this->_mail === null) {
			//TODO: Read Config from backend or database
			$this->_mail = new Zend_Mail();
			$this->_mail->setFrom('hackathon@icyapp.de', 'Einige Sender');
			$this->_mail->addTo('Karl.Spies@gmx.net', 'Einige Empfänger');
		}
		return $this->_mail;
	}

	public function getTransport() {
		if($this->transport === null) {
			//TODO: Read Config from backend or database
			$config = array('auth' => 'login',
							'username' => 'm02439fd',
							'password' => 'hackathon2012');
			$this->transport = new Zend_Mail_Transport_Smtp('icyapp.de', $config);
		}
		return $this->transport;
	}
}
