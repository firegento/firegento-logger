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
 * Log JS errors to the backend
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_ErrorController extends Mage_Core_Controller_Front_Action
{
    /**
     * send a js error to the backend
     */
    public function sendAction()
    {
        foreach (explode(';', Mage::getStoreConfig('logger/general/frontend_bots')) as $agent) {
            if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], trim($agent)) !== false) {
                return;
            }
        }

        $params = $this->getRequest()->getPost();
        $message = '';
        foreach ($params as $paramKey => $paramValue) {
            $message .= $paramKey.': '.$paramValue."\n";
        }
        Mage::log($message, Zend_Log::ERR, 'js_error.log');
    }
}
