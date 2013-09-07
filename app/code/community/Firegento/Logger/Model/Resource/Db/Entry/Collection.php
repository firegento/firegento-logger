<?php
class Firegento_Logger_Model_Resource_Db_Entry_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('firegento_logger/db_entry', 'entity_id');
    }
}
