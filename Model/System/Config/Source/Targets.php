<?php
/**
 * Created by JetBrains PhpStorm.
 * User: damian
 * Date: 31.03.12
 * Time: 15:36
 * To change this template use File | Settings | File Templates.
 */
class Hackathon_Logger_Model_System_Config_Source_Targets extends Varien_Object
{
	private $_options = array();

	const ZEND_LOG_WRITER_STREAM = "Zend_Log_Writer_Stream";

	public function _construct()
	{
		$helper = Mage::helper('hackathon_logger');
		$this->_options[] = array('label' => $helper->__('Mail'), 'value' => 'mail', 'class' => 'Hackathon_Logger_Model_Mail');
		$this->_options[] = array('label' => $helper->__('DB'), 'value' => 'db', 'class' => 'Hackathon_Logger_Model_Db');
		$this->_options[] = array('label' => $helper->__('XMPP'), 'value' => 'xmpp', 'class' => 'Hackathon_Logger_Model_Xmpp');
		$this->_options[] = array('label' => $helper->__('File'), 'value' => 'file', 'class' => self::ZEND_LOG_WRITER_STREAM);
		$this->_options[] = array('label' => $helper->__('Dropbox'), 'value' => 'dropbox', 'class' => 'Hackathon_Logger_Model_Dropbox');
	}
	/**
	 * @return array
	 */
	public function toOptionArray()
	{
		return $this->_options;
	}
	/**
	 * Get the actual Class for the stored Backend value
	 * @param $optionValue
	 * @return string
	 */
	public function optionToClass($optionValue)
	{
		$current = self::ZEND_LOG_WRITER_STREAM;
		foreach ($this->_options as $option) {
			if ($option['value'] == $optionValue) {
				$current = $option['class'];
			}
		}
		return $current;
	}
	/**
	 * Set the actual options array. Should be in the format array('label' => '', 'value' => '', 'class' => '')
	 * @param $options
	 */
	public function setOptions($options)
	{
		$this->_options = $options;
	}

	public function addOption($label,$value, $className) {
		$this->_options[] = array('label' => $label, 'value' => $value, 'class' => $className);
	}
}