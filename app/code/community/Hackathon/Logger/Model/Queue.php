<?php
/**
 * This writer is the one actually used by Magento. It acts as a proxy to support one or more writers
 * set from the config and optionally as a "queue" to hold all events until shutdown.
 */
class Hackathon_Logger_Model_Queue extends Zend_Log_Writer_Abstract
{

	/** @var Zend_Log_Writer_Abstract[] */
	protected $_writers = array();

	private $_logger_cache = array();

	protected $_useQueue;

	protected static $_advancedFormatter;

	/**
	 * @param string $filename
	 */
	public function __construct($filename)
	{
		/** @var $helper Hackathon_Logger_Helper_Data */
		$helper = Mage::helper('hackathon_logger');

		// Only instantiate writers that are needed for this file based on the Filename Filters
		$targets = explode(',', $helper->getLoggerConfig('general/targets'));
		if ($targets) {
			$mappedTargets = $helper->getMappedTargets(basename($filename));
			if ($mappedTargets === NULL) { // No filters, enable backtrace for all targets
				$mappedTargets = array_fill_keys($targets, TRUE);
			} else {
				$targets = array_intersect($targets, array_keys($mappedTargets));
			}
			foreach($targets as $target) {
				$className = (string) Mage::app()->getConfig()->getNode('global/log/core/writer_models/'.$target.'/class');
				if($className) {
					$writer = new $className($filename);
					$helper->addPriorityFilter($writer, $target.'/priority');
					if (method_exists($writer, 'setEnableBacktrace')) {
							$writer->setEnableBacktrace($mappedTargets[$target]);
					}
					$this->_writers[] = $writer;
				}
			}
		}
		$this->_useQueue = !! $helper->getLoggerConfig('general/use_queue');
	}

	/**
	 * Write a message to the log.
	 *
	 * @param array $event log data event
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
	 * @param Zend_Log_Formatter_Interface $formatter
	 */
	public function setFormatter(Zend_Log_Formatter_Interface $formatter)
	{
		$this->_formatter = self::getAdvancedFormatter();
		foreach ($this->_writers as $writer) {
			if (get_class($writer) == 'Zend_Log_Writer_Stream') { // don't override formatter for default writer
				$writer->setFormatter($formatter);
			} else {
				$writer->setFormatter(self::getAdvancedFormatter());
			}
		}
	}

	/**
	 * @static
	 * @return Hackathon_Logger_Formatter_Advanced
	 */
	public static function getAdvancedFormatter()
	{
		if ( ! self::$_advancedFormatter) { // Use singleton since all instances will be identical anyway
			self::$_advancedFormatter = new Hackathon_Logger_Formatter_Advanced;
		}
		return self::$_advancedFormatter;
	}

  /**
   * Satisfy newer Zend Framework
   *
   * @static
   * @param $config
   */
  static public function factory($config) {}

}
