<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Christoph
 * Date: 31.03.12
 * Time: 19:53
 * To change this template use File | Settings | File Templates.
 */
class Hackathon_Logger_Model_Resource_Db_Entry extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('hackathon_logger/db_entry', 'entity_id');
    }
}