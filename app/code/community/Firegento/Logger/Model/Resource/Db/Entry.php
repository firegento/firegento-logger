<?php
class Firegento_Logger_Model_Resource_Db_Entry extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('firegento_logger/db_entry', 'entity_id');
    }
}
