<?php

class FireGento_Logger_Block_Adminhtml_System_Config_EmailNotificationRule
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Prepare fields to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('pattern', array(
            'label' => Mage::helper('firegento_logger')->__('Pattern'),
            'style' => 'width:150px',
        ));

        $severityRenderer = new FireGento_Logger_Block_Adminhtml_System_Config_Renderer_Select;
        $severityRenderer->setValues(
            Mage::getSingleton('firegento_logger/system_config_source_prioritydefault')->toOptionArray()
        );
        $this->addColumn('severity', array(
            'label' => Mage::helper('firegento_logger')->__('Severity'),
            'style' => 'width:100px',
            'renderer' => $severityRenderer,
        ));

        $this->addColumn('email_list_csv', array(
            'label' => Mage::helper('firegento_logger')->__('Email(s)'),
            'style' => 'width:150px',
        ));

        $this->_addButtonLabel = Mage::helper('firegento_logger')->__('Add Notification Rule');
    }
}
