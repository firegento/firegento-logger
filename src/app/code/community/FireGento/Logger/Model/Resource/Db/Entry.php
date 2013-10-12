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
 * Db Entry Resource Model
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Resource_Db_Entry extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Init main table and id field name
     */
    public function _construct()
    {
        $this->_init('firegento_logger/db_entry', 'entity_id');
    }

    /**
     * Clean the log table
     *
     * @param  int $keepDays Days to keep
     * @return int The number of deleted rows
     */
    public function cleanLogs($keepDays)
    {
        if (!$keepDays) {
            return 0;
        }

        $time = Mage::getModel('core/date')->gmtTimestamp() - (60 * 60 * 24 * $keepDays);
        $delete = Varien_Date::formatDate($time, false);

        return $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto('timestamp < ?', $delete)
        );
    }
}
