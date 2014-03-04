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
 * Upgrade script to update advanced formater log format.
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$advancedFormatPath = "logger/general/format";
$advancedFormat = "%timestamp% %priorityName% (%storeCode%): %requestMethod% %requestUri%
REQUEST: %requestData%
TIME: %timeElapsed%s
ADDRESS: %remoteAddress%
USER AGENT: %httpUserAgent%
FILE: %file%:%line%
%message%";

$installer->setConfigData($advancedFormatPath, $advancedFormat);

$installer->endSetup();
