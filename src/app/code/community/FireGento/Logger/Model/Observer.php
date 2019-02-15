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
 * Observer Class
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Observer extends Varien_Object
{
    /**
     * Constant how long the files should be kept on the filesystem before they are rotated
     */
    const MAX_FILE_DAYS = 30;

    /**
     * @var bool
     */
    protected $init = true;

    /**
     * Cron job for cleaning firegento log table
     */
    public function cleanLogsCron()
    {
        Mage::getResourceSingleton('firegento_logger/db_entry')->cleanLogs(
            Mage::helper('firegento_logger')->getMaxDaysToKeep()
        );
    }

    /**
     * Rotate all files in var/log which ends with .log
     */
    public function rotateLogs()
    {
        $var = Mage::getBaseDir('log');

        $logDir = new Varien_Io_File();
        $logDir->cd($var);
        $logFiles = $logDir->ls(Varien_Io_File::GREP_FILES);

        foreach ($logFiles as $logFile) {
            if ($logFile['filetype'] == 'log') {
                $filename = $logFile['text'];
                if (extension_loaded('zlib')) {
                    $zipname = $var . DS . $this->getArchiveName($filename);
                    $zip = gzopen($zipname, 'wb9');
                    gzwrite($zip, $logDir->read($filename));
                    gzclose($zip);
                } else {
                    $logDir->cp($filename, $this->getArchiveName($filename));
                }
                foreach ($this->getFilesOlderThan(self::MAX_FILE_DAYS, $var, $filename) as $oldFile) {
                    $logDir->rm($oldFile['text']);
                }
                $logDir->rm($filename);
            }
        }
        $logDir->close();
    }

    /**
     * Get all files which are older than X days and containing a pattern.
     *
     * @param  int $days Days
     * @param  string $dir Directory
     * @param  string $filename Filename
     * @return array
     */
    public function getFilesOlderThan($days, $dir, $filename)
    {
        $date = Mage::getModel('core/date')->gmtTimestamp() - (60 * 60 * 24 * $days);

        $oldFiles = array();
        $scanDir = new Varien_Io_File();
        $scanDir->cd($dir);
        foreach ($scanDir->ls(Varien_Io_File::GREP_FILES) as $oldFile) {
            if (stripos($oldFile['text'], $filename) != false && strtotime($oldFile['mod_date']) < $date) {
                $oldFiles[] = $oldFile;
            }
        }

        return $oldFiles;
    }

    /**
     * Create a zip filename out of a filename with timestamp
     *
     * @param  string $filename Filename
     * @return string
     */
    protected function getArchiveName($filename)
    {
        $date = $this->formatDate(Mage::getModel('core/date')->gmtTimestamp());

        $extension = '';
        if (extension_loaded('zlib')) {
            $extension = '.gz';
        }
        $filename = $filename . "_" . $date . $extension;

        return $filename;
    }

    /**
     * Format date to internal format
     *
     * @param  string|Zend_Date $date Date to format
     * @return string Formatted date
     */
    public function formatDate($date)
    {
        return Varien_Date::formatDate($date, false);
    }

    /**
     * add some inline javascript to the head (before anything else)
     * to enable javascript logging for all following js code
     *
     * the alternative would be to rewrite html/head or change the template files, which is both worse ;)
     *
     * @param Varien_Event_Observer $observer
     */
    public function addLoggerJs(Varien_Event_Observer $observer)
    {
        if ($this->init && Mage::helper('core')->isDevAllowed()) {
            $this->init = false;
        }

        if ($this->init && $observer->getBlock()->getNameInLayout() === 'head'
            && (boolean)Mage::helper('firegento_logger')->getLoggerConfig('general/frontend_logger')
        ) {
            $transport = $observer->getTransport();
            $html = $transport->getHtml();
            $block = Mage::app()->getLayout()->createBlock('core/template');
            $block->setTemplate('firegento_logger/js.phtml');
            $html = $block->toHtml() . $html;
            $transport->setHtml($html);

            $this->init = false;
        }
    }

    /**
     * Predispatch controller action and before cron job
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function initLoggerClient(Varien_Event_Observer $observer)
    {
        static $done;
        if ( ! $done) {
            $done = TRUE;

            // Install logger clients if needed
            $targets = Mage::helper('firegento_logger')->getAllTargets();

            // Allow Sentry to capture all errors, not just Mage::log
            if (in_array('sentry', $targets)) {
                try {
                    Mage::getModel('firegento_logger/sentry')->initRavenClient();
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }
    }

}
