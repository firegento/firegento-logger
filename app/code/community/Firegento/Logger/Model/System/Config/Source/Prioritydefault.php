<?php
class Firegento_Logger_Model_System_Config_Source_Prioritydefault extends Firegento_Logger_Model_System_Config_Source_Priorities
{
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $helper = Mage::helper('firegento_logger');
        array_unshift($options, array('label' => $helper->__('Default'), 'value' => 'default'));
        return $options;
    }
}
