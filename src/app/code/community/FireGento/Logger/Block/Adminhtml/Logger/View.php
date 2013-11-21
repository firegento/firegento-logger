<?php

class FireGento_Logger_Block_Adminhtml_Logger_View extends Mage_Core_Block_Template
{
    /**
     * @return FireGento_Logger_Model_Db_Entry
     */
    public function getLoggerEntry()
    {
        return Mage::registry('current_loggerentry');
    }
}