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
 * Log priorities
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_System_Config_Source_Priorities
{
    /**
     * Retrieve all priority options
     *
     * @return array Priorities
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('firegento_logger');

        return array(
            array('label' => $helper->__('Emergency'), 'value' => (string)Zend_Log::EMERG),
            array('label' => $helper->__('Alert'), 'value' => Zend_Log::ALERT),
            array('label' => $helper->__('Critical'), 'value' => Zend_Log::CRIT),
            array('label' => $helper->__('Error'), 'value' => Zend_Log::ERR),
            array('label' => $helper->__('Warning'), 'value' => Zend_Log::WARN),
            array('label' => $helper->__('Notice'), 'value' => Zend_Log::NOTICE),
            array('label' => $helper->__('Info'), 'value' => Zend_Log::INFO),
            array('label' => $helper->__('Debug'), 'value' => Zend_Log::DEBUG),
        );
    }
}
