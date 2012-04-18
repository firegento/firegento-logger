<?php
/**
 * @author Colin Mollenhour
 */
class Hackathon_Logger_Model_System_Config_Source_Prioritydefault extends Hackathon_Logger_Model_System_Config_Source_Priorities
{
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $helper = Mage::helper('hackathon_logger');
        array_unshift($options, array('label' => $helper->__('Default'), 'value' => 'default'));
        return $options;
    }
}
