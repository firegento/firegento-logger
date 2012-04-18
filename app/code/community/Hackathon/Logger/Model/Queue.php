<?php
/**
 * User: spies
 * Date: 31.03.12 (13 KW)
 * Time: 20:58
 */
class Hackathon_Logger_Model_Queue extends Zend_Log_Writer_Abstract
{

	/** @var Zend_Log_Writer_Abstract[] */
	protected $_writers = null;

	private $_logger_cache = array();

	protected $_useQueue;

	public function __construct($filename)
	{
		/** @var $helper Hackathon_Logger_Helper_Data */
		$helper = Mage::helper('hackathon_logger');
		$targets = $helper->getLoggerConfig('queue/targets');
		foreach(explode(',', $targets) as $target) {
			$className = (string) Mage::app()->getConfig()->getNode('global/writer_models/'.$target.'/class');
			if($className) {
				$this->_writers = new $className($filename);
			}
		}
		$this->_useQueue = Mage::getStoreConfigFlag('logger/queue/use_queue');
	}

	/**
	 * Write a message to the log.
	 *
	 * @param  array  $event  log data event
	 * @return void
	 */
	protected function _write($event)
	{
		if ($this->_useQueue) {
			// Format now so that timestamps are correct
			$this->_logger_cache[] = $this->_formatter->format($event);
		} else {
			foreach ($this->_writers as $writer) {
				$writer->write($event);
			}
		}
	}

	/**
	 * At the end of the request we write to the actual logger
	 *
	 * @return void
	 */
	public function shutdown()
	{
		$events = implode(PHP_EOL, $this->_logger_cache);
		foreach ($this->_writers as $writer) {
			if ($events) {
				$writer->write($events);
			}
			$writer->shutdown();
		}
	}

	/**
	 * Overrode this method since Mage::log doesn't let us set a formatter any other way.
	 *
	 * @param  Zend_Log_Formatter_Interface $formatter
	 */
	public function setFormatter($formatter)
	{
		$this->_formatter = new Hackathon_Logger_Formatter_Advanced;
	}

}
