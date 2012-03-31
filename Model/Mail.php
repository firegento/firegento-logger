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

	public function __construct($filename) {
		$mail = new Zend_Mail();
		parent::__construct($mail);
	}
}
