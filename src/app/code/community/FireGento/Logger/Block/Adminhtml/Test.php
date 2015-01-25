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
 * @copyright 2015 FireGento Team (http://www.firegento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
/**
 * Test button
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Block_Adminhtml_Test
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml (Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $buttonHtml = $this->_getAddRowButtonHtml($this->__('Run Test'));
        return $buttonHtml;
    }

    protected function _getAddRowButtonHtml ($title)
    {

        $buttonBlock = $this->getElement()
            ->getForm()
            ->getParent()
            ->getLayout()
            ->createBlock('adminhtml/widget_button');

        $_websiteCode = $buttonBlock->getRequest()
            ->getParam('website', null);

        $params = array();

        if (!empty($_websiteCode)) {
            $params['website'] = $_websiteCode;
        }

        $url = Mage::helper('adminhtml')
            ->getUrl("*/logger_test/index", $params);

        $buttonHtml = $this->getLayout()
            ->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setLabel($this->__($title))
            ->setOnClick("window.location.href='" . $url . "'")
            ->toHtml();

        return $buttonHtml;
    }
}