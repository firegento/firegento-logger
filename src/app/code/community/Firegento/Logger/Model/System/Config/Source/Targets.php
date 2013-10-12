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
 * Logging Targets
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_Logger_Model_System_Config_Source_Targets
{
    /**
     * @var array Options
     */
    protected $_options = array();

    /**
     * Retrieve all targets as option arry
     *
     * @return array Targets
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            foreach (Mage::app()->getConfig()->getNode('global/log/core/writer_models')->children() as $writer) {
                $module = isset($writer->label['module']) ? $writer->label['module'] : 'firegento_logger';
                $label = Mage::helper($module)->__((string)$writer->label);
                $this->_options[] = array('label' => $label, 'value' => $writer->getName());
            }
        }

        return $this->_options;
    }

}
