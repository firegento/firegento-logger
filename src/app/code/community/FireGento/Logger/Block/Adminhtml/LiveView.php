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
 * Block for live viewing the log files in the backend
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Block_Adminhtml_LiveView extends Mage_Adminhtml_Block_Template
{
    /**
     * Method reads all log-Files in the var/log-folder
     *
     * @return array
     */
    public function getLogFiles()
    {
        $logFiles = array();
        $logFolderPath = Mage::getBaseDir('var') . DS . 'log';

        if (!file_exists($logFolderPath)) {
            mkdir($logFolderPath, 0755, true);
        }

        $directory = new DirectoryIterator($logFolderPath);

        foreach ($directory as $fileInfo) {
            if (!$fileInfo->isFile() || !preg_match('/\.(?:log)$/', $fileInfo->getFilename())) {
                continue;
            }

            $logFiles[] = $fileInfo->getFilename();
        }

        return $logFiles;
    }
}
