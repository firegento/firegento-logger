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
 * cURL methods
 *
 * @see https://docs.sentry.io/clients/php/config/ "curl_method" section
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_System_Config_Source_Curlmethods
{
    /**
     * Retrieve all cURL methods
     *
     * @return array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('firegento_logger');

        return array(
            array('label' => $helper->__('sync'), 'value' => 'sync'),
            array('label' => $helper->__('async (default)'), 'value' => 'async'),
            array('label' => $helper->__('exec'), 'value' => 'exec'),
        );
    }
}