<?php
/**
 * User: cjhbabel
 * Date: 01.04.12 (13 KW)
 * Time: 12:18
 * To change this template use File | Settings | File Templates.
 */
require_once BP . DS . 'lib' . DS  . 'Graylog2-php-gelf' . DS . 'GELFMessage.php';
require_once BP . DS . 'lib' . DS  . 'Graylog2-php-gelf' . DS . 'GELFMessagePublisher.php';

class Hackathon_Logger_Model_Graylog2 extends Zend_Log_Writer_Abstract
{
	/**
	 * Array of formatted events to include in message body.
	 *
	 * @var array
	 */
	protected $_eventsToSend = array();

	/**
	 * Array ofGraylog2 connection information. default to localhost
	 *
	 * @var array
	 */
	public $options = array(
		'hostname' => 'localhost' );

	/**
	 * @param array $optionsGRaylog2 connection information, mandatory: host
	 * @return void
	 */
	public function __construct($filename)
	{
		$this->setFormatter(new Zend_Log_Formatter_Simple());
        	$helper = Mage::helper('hackathon_logger');
        	$this->options['hostname'] = $helper->getLoggerConfig('graylog2/hostname');
        	$this->options['filename'] = $filename;
    	}

	/**
	 * Construct a Zend_Log driver forGraylog2 servers
	 *
	 * @param  array|Zend_Config $config
	 * @return Zend_Log_FactoryInterface
	 */
	static public function factory($config)
	{
		$config = self::_parseConfig($config);
		if (!isset($config['remoteIP'])) {
			throw new InvalidArgumentException();
		}

		$instance = new self($config['remoteIP']);

		foreach ($config as $key => $value) {
			if (method_exists($instance, 'set' . ucfirst($key))) {
				$instance->{'set' . ucfirst($key)}($value);
			}
		}

		return $instance;
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


		// Finally, send the Event, but re-throw any exceptions at the
		// proper level of abstraction.
		try {
			$graylog2 = new GELFMessagePublisher($this->options['hostname']);

			$msg = new GELFMessage();
			//$msg->setShortMessage('something is broken.');
			$msg->setFullMessage(implode('', $this->_eventsToSend));
			//$msg->setHost('somehost');
			$msg->setLevel(2);
			$msg->setFile($this->options['filename']);
			//$msg->setLine(1337);
			//$msg->setAdditional("something", "foo");
			//$msg->setAdditional("something_else", "bar");

			$graylog2->publish($msg);
		} catch (Exception $e) {
			throw new Zend_Log_Exception(
				$e->getMessage(),
				$e->getCode());
		}
	}
}