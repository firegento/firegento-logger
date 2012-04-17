<?php
/**
 * User: cjhbabel
 * Date: 01.04.12 (13 KW)
 * Time: 12:18
 */
require_once 'lib/Graylog2-gelf-php/GELFMessage.php';
require_once 'lib/Graylog2-gelf-php/GELFMessagePublisher.php';

class Hackathon_Logger_Model_Graylog2 extends Zend_Log_Writer_Abstract
{
	/**
	 * Array of formatted events to include in message body.
	 *
	 * @var array
	 */
	protected $_eventsToSend = array();

	/**
	 * @var array
	 */
	public $options = array();

  /**
   * @param string $filename
   * @return \Hackathon_Logger_Model_Graylog2
   */
	public function __construct($filename)
	{
		$helper = Mage::helper('hackathon_logger'); /* @var $helper Hackathon_Logger_Helper_Data */
		$this->options['hostname'] = $helper->getLoggerConfig('graylog2/hostname');
		$this->options['port'] = $helper->getLoggerConfig('graylog2/port');
		$this->options['chunk_size'] = $helper->getLoggerConfig('graylog2/chunk_size');
		$this->options['filename'] = $filename;
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
			$graylog2 = new GELFMessagePublisher($this->options['hostname'], $this->options['port'], $this->options['chunk_size']);

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
