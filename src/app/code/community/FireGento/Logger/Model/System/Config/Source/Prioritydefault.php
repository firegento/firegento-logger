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
class FireGento_Logger_Model_System_Config_Source_Prioritydefault
    extends FireGento_Logger_Model_System_Config_Source_Priorities
{
    /**
     * Retrieve all priorities with a default value
     *
     * @return array Priorities
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $helper = Mage::helper('firegento_logger');
        array_unshift($options, array('label' => $helper->__('Default'), 'value' => 'default'));

        return $options;
    }
}
