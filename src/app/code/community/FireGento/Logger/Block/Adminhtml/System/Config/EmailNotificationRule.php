<?php
/**
 * This file is part of a FireGento e.V. module.
 *
 * This FireGento e.V. module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * PHP version 5
 *
 * @category  FireGento
 * @package   FireGento_Logger
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2013 FireGento Team (http://www.firegento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
/**
 * Add the abillity to send email notifications after logging to database.
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
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

        $severityRenderer = new FireGento_Logger_Block_Adminhtml_System_Config_Renderer_Select();
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
