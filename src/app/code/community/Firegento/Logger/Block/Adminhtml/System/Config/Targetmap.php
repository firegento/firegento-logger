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
 * Target Map Block for system config
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Block_Adminhtml_System_Config_Targetmap
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * Prepare fields to render
     */
    protected function _prepareToRender()
    {
        $this->addColumn('pattern', array(
            'label' => Mage::helper('firegento_logger')->__('Pattern'),
            'style' => 'width:200px',
        ));

        $targetRenderer = new FireGento_Logger_Block_Adminhtml_System_Config_Renderer_Select;
        $targetRenderer->setValues(
            Mage::getSingleton('firegento_logger/system_config_source_targets')->toOptionArray()
        );
        $this->addColumn('target', array(
            'label' => Mage::helper('firegento_logger')->__('Target'),
            'style' => 'width:180px',
            'renderer' => $targetRenderer,
        ));

        $btRenderer = new FireGento_Logger_Block_Adminhtml_System_Config_Renderer_Select;
        $btRenderer->setValues(Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray());
        $this->addColumn('backtrace', array(
            'label' => Mage::helper('firegento_logger')->__('Backtrace'),
            'style' => 'width:60px',
            'renderer' => $btRenderer,
        ));

        $somRenderer = new FireGento_Logger_Block_Adminhtml_System_Config_Renderer_Select;
        $somRenderer->setValues(Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray());
        $this->addColumn('stop_on_match', array(
            'label' => Mage::helper('firegento_logger')->__('Stop On Match'),
            'style' => 'width:60px',
            'renderer' => $somRenderer,
        ));

        $this->_addButtonLabel = Mage::helper('firegento_logger')->__('Add Target Rule');
    }

    /**
     * Return the targetmap html
     *
     * @return string
     */
    protected function _toHtml()
    {
        // Make sure id is set before template is rendered or else we can't know the id.
        if (!$this->getHtmlId()) {
            $this->setHtmlId('_' . uniqid());
        }
        $html = parent::_toHtml();

        // Scripts in the template must be evaluated so that select values can be set.
        $html .= "
            <script type='text/javascript'>
            arrayRow{$this->getHtmlId()}._add = arrayRow{$this->getHtmlId()}.add;
            arrayRow{$this->getHtmlId()}.add = function(templateData, insertAfterId) {
              this._add(templateData, insertAfterId);
              this.template.evaluate(templateData).evalScripts();
            }
            </script>
        ";
        return $html;
    }
}
