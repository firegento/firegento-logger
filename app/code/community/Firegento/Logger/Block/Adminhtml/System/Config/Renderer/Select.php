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
 * Target Map Select Field Block for system config
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_Logger_Block_Adminhtml_System_Config_Renderer_Select
    extends Mage_Core_Block_Abstract
{
    /**
     * Return the select html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $htmlId = $this->getColumnName() . '#{_id}';
        $select = new Varien_Data_Form_Element_Select(array(
            'html_id' => $htmlId,
            'no_span' => true,
            'name' => $this->getInputName(),
        ));
        $select->addData($this->getColumn());
        $select->setForm(new Varien_Object());
        $select->setValues($this->getValues());

        // Escape properly and use javascript to set the selected values
        $javascriptHtml = "
            {$select->getElementHtml()}
            <script type=\"text\/javascript\">
              $(\"$htmlId\").setValue(\"#{{$this->getColumnName()}}\");
            </script>
        ";
        return str_replace(array("\n", '"', '/'), array('', '\"', '\/'), $javascriptHtml);
    }

}
