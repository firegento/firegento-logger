<?php
/**
 * User: spies
 * Date: 31.03.12 (13 KW)
 * Time: 15:18
 */
require_once 'lib/XMPPHP/XMPP.php';

class Hackathon_Logger_Model_Xmpp extends Zend_Log_Writer_Abstract
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
		'recipient' => '');

  /**
   * @param $filename
   * @return \Hackathon_Logger_Model_Xmpp
   */
	public function __construct($filename)
	{
		$this->setFormatter(new Zend_Log_Formatter_Simple());
        $helper = Mage::helper('hackathon_logger');

        $this->options['host'] = $helper->getLoggerConfig('xmpp/host');
        $this->options['port'] = $helper->getLoggerConfig('xmpp/port');
        $this->options['user'] = $helper->getLoggerConfig('xmpp/username');
        $this->options['password'] = $helper->getLoggerConfig('xmpp/password');
        $this->options['resource'] = Mage::app()->getStore()->getName();
        $this->options['server'] = $helper->getLoggerConfig('xmpp/domain');
        $this->options['recipient'] = $helper->getLoggerConfig('xmpp/recipient');

    }

	/**
	 * Places event line into array of lines to be used as message body.
	 *
	 *
	 * @param  array $event Event data
	 * @return void
	 */
	protected function _write($event)
	{
		$formattedEvent = $this->_formatter->format($event);

		$this->_eventsToSend[] = $formattedEvent;
	}

	/**
	 * Sends message recipient if log entries are present.
	 *
	 * @return void
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
				 XMPPHP_Log::LEVEL_VERBOSE);


				 try {
    				 $jabber->connect();
				     $jabber->processUntil('session_start');
				     $jabber->presence();
				     $events = implode('', $this->_eventsToSend);
				     $jabber->message($this->options['recipient'], $events);
				     $jabber->disconnect();
				 } catch(XMPPHP_Exception $e) {
    				      die($e->getMessage());
				 }
		} catch (Exception $e) {
			throw new Zend_Log_Exception(
				$e->getMessage(),
				$e->getCode());
		}
	}

  /**
   * Satisfy newer Zend Framework
   *
   * @static
   * @param $config
   */
  static public function factory($config) {}

}
