<?php
/**
 * Created by JetBrains PhpStorm.
 * User: spies
 * Date: 31.03.12 (13 KW)
 * Time: 20:08
 */
class Hackathon_Logger_Model_Observer extends Varien_Object
{
	public function loadConfigFromBackend (Varien_Event_Observer $observer)
	{
		/** @var $helper Hackathon_Logger_Helper_Data */
		$helper = Mage::helper('hackathon_logger');
		$value = $helper->getLoggerConfig('targets/targets_value');
		$conf = Mage::getConfig()->setNode('global/log/core/writer_model',$value,true);

	}
}
