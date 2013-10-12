<?php
class Firegento_Logger_Model_Resource_Db_Entry extends Mage_Core_Model_Resource_Db_Abstract
{

    public function _construct()
    {
        $this->_init('firegento_logger/db_entry', 'entity_id');
    }

    /**
     * @param  int $keepDays
     * @return int
     */
    public function cleanLogs($keepDays)
    {
        if (! $keepDays) {
            return 0;
        }
        $delete = Varien_Date::formatDate(Mage::getModel('core/date')->gmtTimestamp() - (60 * 60 * 24 * $keepDays), FALSE);
        return $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto('timestamp < ?', $delete)
        );
    }
}
