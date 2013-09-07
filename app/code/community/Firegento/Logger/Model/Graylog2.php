<?php
require_once 'lib/Graylog2-gelf-php/GELFMessage.php';
require_once 'lib/Graylog2-gelf-php/GELFMessagePublisher.php';

class Firegento_Logger_Model_Graylog2 extends Zend_Log_Writer_Abstract
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
	 * @var bool
	 */
	protected $_enableBacktrace = FALSE;

	/**
	 * @param bool $flag
	 */
	public function setEnableBacktrace($flag)
	{
		$this->_enableBacktrace = $flag;
	}

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
	 * @return \Firegento_Logger_Model_Graylog2
	 */
	public function __construct($filename)
	{
		$helper = Mage::helper('firegento_logger'); /* @var $helper Firegento_Logger_Helper_Data */
		$this->_options['filename'] = basename($filename);
		$this->_options['app_name'] = $helper->getLoggerConfig('graylog2/app_name');
		$hostname = $helper->getLoggerConfig('graylog2/hostname');
		$port = $helper->getLoggerConfig('graylog2/port');
		$chunk_size = $helper->getLoggerConfig('graylog2/chunk_size');
		$this->_publisher = self::getPublisher($hostname, $port, $chunk_size);
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
			Mage::helper('firegento_logger')->addEventMetadata($event);

			$eofMessageFirstLine = strpos($event['message'], "\n");
			$shortMessage = (FALSE === $eofMessageFirstLine) ? $event['message'] :
			substr($event['message'], 0, $eofMessageFirstLine);

			$msg = new GELFMessage();
			$msg->setTimestamp(microtime(TRUE));
			$msg->setShortMessage($shortMessage);
			if ($event['backtrace']) {
				$msg->setFullMessage($event['message']."\n\nBacktrace:\n".$event['backtrace']);
			} else {
				$msg->setFullMessage($event['message']);
			}
			$msg->setHost(gethostname());
			$msg->setLevel($event['priority']);
			$msg->setFacility($this->_options['app_name'] . $this->_options['filename']);
			$msg->setFile($event['file']);
			$msg->setLine($event['line']);
			$msg->setAdditional('store_code', $event['store_code']);
			$msg->setAdditional('time_elapsed', $event['time_elapsed']);
			$msg->setHost(php_uname('n'));
			foreach(array('REQUEST_METHOD', 'REQUEST_URI', 'REMOTE_IP', 'HTTP_USER_AGENT') as $key) {
				if ( ! empty($event[$key])) {
					$msg->setAdditional($key, $event[$key]);
				}
			}

			$this->_publisher->publish($msg);
		}
		catch (Exception $e) {
			throw new Zend_Log_Exception($e->getMessage(),$e->getCode());
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
