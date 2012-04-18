<?php
/**
 * User: cjhbabel
 * Date: 01.04.12 (13 KW)
 * Time: 12:18
 *
 * @author Colin Mollenhour (rewrote to publish each message individually with lots of metadata)
 */
require_once 'lib/Graylog2-gelf-php/GELFMessage.php';
require_once 'lib/Graylog2-gelf-php/GELFMessagePublisher.php';

class Hackathon_Logger_Model_Graylog2 extends Zend_Log_Writer_Abstract
{
	/**
	 * @var array
	 */
	protected $_options = array();

	/**
	 * @var GELFMessagePublisher
	 */
	protected $_publisher;

	/**
	 * @var GELFMessagePublisher[]
	 */
	protected static $_publishers = array();

	/**
	 * Use static method so all loggers share same publisher
	 *
	 * @static
	 * @param $hostname
	 * @param $port
	 * @param $chunk_size
	 * @return GELFMessagePublisher
	 */
	protected static function getPublisher($hostname, $port, $chunk_size)
	{
		$key = "$hostname$port$chunk_size";
		if ( ! isset(self::$_publishers[$key])) {
			self::$_publishers[$key] = new GELFMessagePublisher($hostname, $port, $chunk_size);
		}
		return self::$_publishers[$key];
	}

	/**
	 * @param string $filename
	 * @return \Hackathon_Logger_Model_Graylog2
	 */
	public function __construct($filename)
	{
		$helper = Mage::helper('hackathon_logger'); /* @var $helper Hackathon_Logger_Helper_Data */
		$this->_options['filename'] = $filename;
		$this->_options['app_name'] = $helper->getLoggerConfig('graylog2/app_name');
		$hostname = $helper->getLoggerConfig('graylog2/hostname');
		$port = $helper->getLoggerConfig('graylog2/port');
		$chunk_size = $helper->getLoggerConfig('graylog2/chunk_size');
		$this->_publisher = self::getPublisher($hostname, $port, $chunk_size);
		$helper->addPriorityFilter($this, 'logger/graylog2/priority');
	}

	/**
	 * Places event line into array of lines to be used as message body.
	 *
	 * @param array $event Event data
	 * @return void
	 */
	protected function _write($event)
	{
		try {
			$msg = new GELFMessage();
			$msg->setTimestamp(microtime(TRUE));
			$msg->setShortMessage(substr($event['message'],0,strpos($event['message'],"\n")));
			$msg->setFullMessage($event['message']);
			$msg->setHost(gethostname());
			$msg->setLevel($event['priority']);
			$msg->setFacility($this->_options['app_name'] . '/' . $this->_options['filename']);

			// Find file and line where message originated from
			$nextIsFirst = FALSE;
			foreach(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $frame) {
				if ($frame['type'] == '::' && $frame['class'] == 'Mage' && substr($frame['function'], 0, 3) == 'log') {
					$nextIsFirst = TRUE;
					continue;
				}
				if ($nextIsFirst) {
					$msg->setFile($frame['file']);
					$msg->setLine($frame['line']);
					break;
				}
			}

			// Set additional data that may be useful for debug
			$msg->setAdditional('store_code', Mage::app()->getStore()->getCode());
			if ( ! empty($_SERVER['REQUEST_TIME'])) {
				$msg->setAdditional('time_elapsed', sprintf('%d', time() - $_SERVER['REQUEST_TIME']));
			}
			foreach(array('REQUEST_METHOD', 'REQUEST_URI', 'REMOTE_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_USER_AGENT') as $key) {
				if ( ! empty($_SERVER[$key])) {
					$msg->setAdditional($key, $_SERVER[$key]);
				}
			}

			$this->_publisher->publish($msg);
		}
		catch (Exception $e) {
			throw new Zend_Log_Exception($e->getMessage(),$e->getCode());
		}
	}

}
