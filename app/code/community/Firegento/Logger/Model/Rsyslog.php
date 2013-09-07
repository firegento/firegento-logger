<?php
require_once 'lib/rsyslog/rsyslog.php';

/**
 * Remote Syslog writer. Sends the Log Messages to a Remote Syslog server.
 * Messages are sent as plain text.
 */
class Firegento_Logger_Model_Rsyslog extends Zend_Log_Writer_Abstract {
	// @var int The default Timeout to be used when communicating with the Remote Syslog Server.
	const DEFAULT_TIMEOUT = 1;

	// TODO Allow User to choose the Facility from one of the values provided by SyslogFacility Class.
	// @var int The default Facility used to build Syslog Messages.
	const DEFAULT_FACILITY = SyslogFacility::USER;

	// The properties below will be set automatically by Log4php with the data it
	// will get from the configuration.
	// @var string The address of the RSyslog log to which the log messages will be sent.
	protected $HostName;
	// @var int The port to use to connect to RSyslog server.
	protected $Port;
	// @var int Timeout tro be used when communicating with Remote SysLog Server
	protected $Timeout;

	// @var array Contains configuration options.
	protected $_options = array();

	// @var bool Indicates if backtrace should be added to the Log Message.
	protected $_enableBacktrace = FALSE;

	/**
	 * Setter for _enableBacktrace field.
	 *
	 * @param bool flag The value to assign to the field.
	 */
	public function setEnableBacktrace($flag) {
		$this->_enableBacktrace = $flag;
	}

	/**
	 * Builds and returns the full URL where the Log messages will be sent.
	 *
	 * @return string The full URL where the Log messages will be sent.
	 */
	protected function GetSyslogPublisher() {
		if(empty($this->SyslogPublisher)) {
			$this->SyslogPublisher = new RSyslog(($this->HostName . ':' . $this->Port),
																					 $this->Timeout);
		}

		return $this->SyslogPublisher;
	}

	/**
	 * Builds a Message that will be sent to a RSyslog Server.
	 *
	 * @param array event A Log4php Event.
	 * @return string A string representing the message.
	 */
	protected function BuildSysLogMessage($event) {
		return new SyslogMessage($this->_formatter->format($event, $this->_enableBacktrace),
														 self::DEFAULT_FACILITY,
														 $event['priority'],
														 strtotime($event['timestamp']));
	}

	/**
	 * Sends a Message to a RSyslog server.
	 *
	 * @param string Message The Message to be sent.
	 * @return bool True if message was sent correctly, False otherwise.
	 */
	protected function PublishMessage($Message) {
		$Result = $this->GetSyslogPublisher()->Send($Message);
		if($Result === true) {
			return true;
		}

		// In case of error, RSysLog publisher returns an array containing an Error Number
		// and an Error Message
		throw new Zend_Log_Exception(sprintf(Mage::helper('firegento_logger')->__('Error occurred sending log to Remote Syslog Server. Error number: %d. Error Message: %s'),
																				 $Result[0],
																				 $Result[1]));
		return false;
	}

	/**
	 * @param string $FileName
	 * @return Firegento_Logger_Model_Logglysyslog
	 */
	public function __construct($FileName) {
		$helper = Mage::helper('firegento_logger'); /* @var $helper Firegento_Logger_Helper_Data */
		$this->_options['FileName'] = basename($FileName);
		$this->_options['AppName'] = $helper->getLoggerConfig('rsyslog/app_name');

		$this->HostName = $helper->getLoggerConfig('rsyslog/hostname');
		$this->Port = $helper->getLoggerConfig('rsyslog/port');
		$this->Timeout = $helper->getLoggerConfig('rsyslog/timeout');
	}

	/**
	 * Places event line into array of lines to be used as message body.
	 *
	 * @param array $event Event data
	 * @return void
	 */
	protected function _write($event) {
		$Message = $this->BuildSysLogMessage($event);

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
