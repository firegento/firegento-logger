<?php
class Firegento_Logger_Model_Observer extends Varien_Object
{
    /** @var Firegento_Logger_Helper_Data */
    private $_helper;

    public function _construct() {
        parent::_construct();
        $this->_helper = Mage::helper('firegento_logger');
    }

    /**
     * Called by cron expression in config.xml to cleanup the
     * logs written to the DB.
     */
    public function clean_logs(Varien_Event_Observer $observer, $days = 0)
    {
        $counter  = 0;
        if($days == 0) {
            $days = $this->getMaximumLogMessagesInDays();
        }
        $delete   = $this->formatDate(Mage::getModel('core/date')
            ->gmtTimestamp() - (60 * 60 * 24 * $days));
        /** @var $messages Firegento_Logger_Model_Resource_Db_Entry_Collection */
        $messages = Mage::getModel('firegento_logger/db_entry')
            ->getCollection()
            ->addFieldToFilter('timestamp', array('lt' => $delete));
        /** @var $message Firegento_Logger_Model_Db_Entry */
        foreach($messages as $message)
        {
            $message->delete();
            $counter++;
        }

        Mage::log('[CRONJOB: clean_logs] Deleted ' . $counter . ' log message(s) from DB that are older than '
        . $this->getMaximumLogMessagesInDays() . ' days.', Zend_Log::INFO);
    }

    /**
     * Format date to internal format
     *
     * @param string|Zend_Date $date
     * @param bool             $includeTime
     *
     * @return string
     */
    public function formatDate($date, $includeTime = true)
    {
        return Varien_Date::formatDate($date, $includeTime);
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
