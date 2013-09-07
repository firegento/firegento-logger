<?php
class Firegento_Logger_Model_Observer extends Varien_Object
{
    /** @var Firegento_Logger_Helper_Data */
    private $_helper;

    public function _construct()
    {
        parent::_construct();
        $this->_helper = Mage::helper('firegento_logger');
    }

    /**
     * Called by cron expression in config.xml to cleanup the
     * logs written to the DB.
     *
     * @param Varien_Event_Observer $observer
     * @param int                   $days
     */
    public function cleanLogs(Varien_Event_Observer $observer, $days = 0)
    {
        $counter = 0;
        if ($days == 0)
        {
            $days = $this->getMaximumLogMessagesInDays();
        }
        $delete = $this->formatDate(Mage::getModel('core/date')
            ->gmtTimestamp() - (60 * 60 * 24 * $days));
        /** @var $messages Firegento_Logger_Model_Resource_Db_Entry_Collection */
        $messages = Mage::getModel('firegento_logger/db_entry')
            ->getCollection()
            ->addFieldToFilter('timestamp', array('lt' => $delete));
        /** @var $message Firegento_Logger_Model_Db_Entry */
        foreach ($messages as $message)
        {
            $message->delete();
            $counter++;
        }

        Mage::log('[CRONJOB: clean_logs] Deleted ' . $counter . ' log message(s) from DB that are older than '
        . $this->getMaximumLogMessagesInDays() . ' days.', Zend_Log::INFO);
    }

    /**
     * Rotate all files in var/log which ends with .log
     *
     * @param Varien_Event_Observer $observer
     */
    public function rotateLogs(Varien_Event_Observer $observer)
    {
        $var = Mage::getBaseDir('log');

        $logDir = new Varien_Io_File();
        $logDir->cd($var);
        $logFiles = $logDir->ls(Varien_Io_File::GREP_FILES);

        foreach ($logFiles as $logFile)
        {
            if ($logFile['filetype'] == 'log')
            {
                $filename = $logFile['text'];
                if (extension_loaded('zlib'))
                {
                    $zipname = $var . DS . $this->getArchiveName($filename);
                    $zip     = gzopen($zipname, 'wb9');
                    gzwrite($zip, $logDir->read($filename));
                    gzclose($zip);
                }
                else
                {
                    $logDir->cp($filename, $this->getArchiveName($filename));
                }
                $logDir->rm($filename);
            }
        }
        $logDir->close();
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

    /**
     * @param $days
     * @param $dir
     */
    protected function getFilesOlderThan($days, $dir)
    {

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
        $date      = $this->formatDate(Mage::getModel('core/date')
            ->gmtTimestamp());
        $extension = '';

        if (extension_loaded('zlib'))
        {
            $extension = '.gz';
        }
        $filename = $filename . "_" . $date . $extension;

        return $filename;
    }

    /**
     * The maximun of days to keep log messages in the database table.
     *
     * @return string
     */
    protected function getMaximumLogMessagesInDays()
    {
        return $this->_helper->getMaxLogMessagesInDays();
    }
}
