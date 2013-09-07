<?php
define('LOGGER_CERTIFICATESFILE', Mage::getModuleDir('', 'Firegento_Logger') . '/extras/certificates/cacert.pem');

class Firegento_Logger_Model_Logglyhttps extends Zend_Log_Writer_Abstract {
	// @var string The URL of Loggly Log Server
	protected $LogglyServer = 'logs.loggly.com';
	// @var int The port to use to communicate with Loggly Server.
	protected $LogglyPort = 443;
	// @var string The Loggly path where to send Log Messages.
	protected $LogglyPath = '/inputs';
	// @var string The SHA Input Key to be used to send Logs to Loggly via HTTPS
	protected $InputKey;

	// @var int The timeout to apply when sending data to Loggly servers, in seconds.
	protected $Timeout = 5;

	// @var array Contains configuration options.
	protected $_options = array();

	// @var bool Indicates if backtrace should be added to the Log Message.
	protected $_enableBacktrace = FALSE;

	/**
	 * @param bool $flag
	 */
	public function setEnableBacktrace($flag) {
		$this->_enableBacktrace = $flag;
	}

	/**
	 * @param string $FileName
	 * @return Firegento_Logger_Model_Logglyhttps
	 */
	public function __construct($FileName) {
		$helper = Mage::helper('firegento_logger'); /* @var $helper Firegento_Logger_Helper_Data */
		$this->_options['FileName'] = basename($FileName);
		$this->_options['AppName'] = $helper->getLoggerConfig('logglyhttps/app_name');

		$this->InputKey = $helper->getLoggerConfig('logglyhttps/inputkey');
		$this->Timeout = $helper->getLoggerConfig('logglyhttps/timeout');
	}

	/**
	 * Builds and returns the full URL where the Log messages will be sent.
	 *
	 * @return string The full URL where the Log messages will be sent.
	 */
	protected function GetLoggerURL() {
		return $this->LogglyURL . '/' . $this->InputKey;
	}

	/**
	 * Builds a JSON Message that will be sent to a Loggly Server.
	 *
	 * @param array event A Magento Log Event.
	 * @param bool enableBacktrace Indicates if a backtrace should be added to the
	 * log event.
	 * @return string A JSON structure representing the message.
	 */
	protected function BuildJSONMessage($event, $enableBacktrace = FALSE) {
    Mage::helper('firegento_logger')->addEventMetadata($event, '-', $enableBacktrace);

		$Fields = array();

		$Fields['Level'] = $event['priority'];
		$Fields['FileName'] = $event['file'];
		$Fields['LineNumber'] = $event['line'];
		$Fields['StoreCode'] = $event['store_code'];
		$Fields['TimeElapsed'] = $event['time_elapsed'];
		$Fields['Host'] = php_uname('n');
		$Fields['TimeStamp'] = date('Y-m-d H:i:s', strtotime($event['timestamp']));
		$Fields['Facility'] = $this->_options['AppName'] . $this->_options['FileName'];

		if ($event['backtrace']) {
			$Fields['Message'] = $event['message']."\n\nBacktrace:\n".$event['backtrace'];
		}
		else {
			$Fields['Message'] = $event['message'];
		}

		foreach(array('REQUEST_METHOD', 'REQUEST_URI', 'REMOTE_IP', 'HTTP_USER_AGENT') as $Key) {
			if (!empty($event[$Key])) {
				$Fields[$Key] = $event[$Key];
			}
		}

		return json_encode($Fields);
	}


	/**
	 * Sends a JSON Message to Loggly.
	 *
	 * @param string Message The JSON-Encoded Message to be sent.
	 * @return bool True if message was sent correctly, False otherwise.
	 */
	protected function PublishMessage($Message) {
		$fp = fsockopen(sprintf('ssl://%s', $this->LogglyServer),
												$this->LogglyPort,
												$ErrorNumber,
												$ErrorMessage,
												$this->Timeout);

		// TODO Replace HTTPS with UDP
		try {
			$Out = sprintf("POST %s/%s HTTP/1.1\r\n",
										 $this->LogglyPath,
										 $this->InputKey);
			$Out .= sprintf("Host: %s\r\n", $this->LogglyServer);
			$Out .= "Content-Type: application/json\r\n";
			$Out .= "User-Agent: Vanilla Logger Plugin\r\n";
			$Out .= sprintf("Content-Length: %d\r\n", strlen($Message));
			$Out .= "Connection: Close\r\n\r\n";
			$Out .= $Message . "\r\n\r\n";

			$Result = fwrite($fp, $Out);
			fclose($fp);

			if($Result == false) {
				throw new Zend_Log_Exception(sprintf(Mage::helper('firegento_logger')->__('Error occurred posting log message to Loggly via HTTPS. Posted Message: %s'),
																						 $Message));
			}
		}
		catch(Exception $e) {
			throw new Zend_Log_Exception($e->getMessage(), $e->getCode());
		}

		return true;
	}

	/**
	 * Places event line into array of lines to be used as message body.
	 *
	 * @param array $event Event data
	 * @return void
	 */
	protected function _write($event) {
		$Message = $this->BuildJSONMessage($event, $this->_enableBacktrace);

		return $this->PublishMessage($Message);
	}

  /**
   * Satisfy newer Zend Framework
   *
   * @static
   * @param $config
   */
  static public function factory($config) {}

}
