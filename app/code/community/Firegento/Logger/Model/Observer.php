<?php
class Firegento_Logger_Model_Observer extends Varien_Object
{

    const MAX_FILE_DAYS = 30;

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
     *
     * @param Varien_Event_Observer|null $observer
     */
    public function rotateLogs($observer = NULL)
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
                    $zip     = gzopen($zipname, 'wb9');
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
     * Get all files which are older than c days and containing a pattern.
     *
     * @param $days
     * @param $dir
     * @param $filename
     *
     * @return array
     */
    public function getFilesOlderThan($days, $dir, $filename)
    {
        $date = Mage::getModel('core/date')
                       ->gmtTimestamp() - (60 * 60 * 24 * $days);
        $oldFiles = array();
        $scanDir = new Varien_Io_File();
        $scanDir->cd($dir);
        foreach ($scanDir->ls(Varien_Io_File::GREP_FILES) as $oldFile) {
            if (stripos($oldFile['text'], $filename) != false && strtotime($oldFile['mod_date']) < $date ) {
                $oldFiles[] = $oldFile;
            }
        }
        return $oldFiles;
    }

    /**
     * Create a zip filename out of a filename with timestamp
     *
     * @param $filename
     *
     * @return string
     */
    protected function getArchiveName($filename)
    {
        $date = $this->formatDate(Mage::getModel('core/date')
            ->gmtTimestamp());
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
     * @param string|Zend_Date $date
     *
     * @return string
     */
    public function formatDate($date)
    {
        return Varien_Date::formatDate($date, false);
    }

}
