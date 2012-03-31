<?php
/**
 * Created by JetBrains PhpStorm.
 * User: spies
 * Date: 31.03.12 (13 KW)
 * Time: 20:58
 * To change this template use File | Settings | File Templates.
 */
class Hackathon_Logger_Model_Queue extends Zend_Log_Writer_Abstract
{
	/** @var Zend_Log_Writer_Abstract */
	private $_logger_mock = null;

	private $_logger_cache = array();

	public function __construct($filename)
	{
		/** @var $helper Hackathon_Logger_Helper_Data */
		$helper = Mage::helper('hackathon_logger');
		$target = $helper->getLoggerConfig('targets/targets_value');
		/** @var $converter Hackathon_Logger_Model_System_Config_Source_Targets */
		$converter = Mage::getModel('hackathon_logger/system_config_source_targets');
		$className = $converter->optionToClass($target);

		$this->_logger_mock = new $className($filename);

		$v = 0;
	}

	/**
	 * Write a message to the log.
	 *
	 * @param  array  $event  log data event
	 * @return void
	 */
	protected function _write($event)
	{
		//we write first to the internal array
		$this->_logger_cache[] = $event;
	}
	/**
	 * At the end of the request we write to the actual logger
	 */
	public function shutdown()
	{
		foreach($this->_logger_cache as $event){
			$this->_logger_mock->_write($event);
		}

        // because Mail has own queue
        $this->_logger_mock->shutdown();
        return parent::shutdown();
	}

	/**
	 * Construct a Zend_Log driver
	 *
	 * @param  array|Zend_Config $config
	 * @return Zend_Log_FactoryInterface
	 */
	static public function factory($config)
	{
		// TODO: Implement factory() method.
	}
}
