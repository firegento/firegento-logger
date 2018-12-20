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
 * Model for Database logging
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Db extends Zend_Log_Writer_Db
{
    /**
     * Class constructor
     *
     * @param string $filename Filename
     */
    public function __construct($filename)
    {
        $resource = Mage::getSingleton('core/resource');
        $this->_db = $resource->getConnection('core_write');
        $this->_table = $resource->getTableName('firegento_logger/db_entry');
        $this->_columnMap = array('severity' => 'priority', 'message' => 'message');
        parent::__construct($this->_db, $this->_table, $this->_columnMap);
    }

    /**
     * Set a custom formatter
     *
     * @param Zend_Log_Formatter_Interface $formatter Formatter
     */
    public function setFormatter(Zend_Log_Formatter_Interface $formatter)
    {
        // ignore formatter as it is not supported for db log writer
    }

    /**
     * Returns log data in advanced format
     *
     * @param  FireGento_Logger_Model_Event $event the log event
     *
     * @return string
     */
    protected function getAdvancedInfo($event)
    {
        $oAdvancedFormatter = new FireGento_Logger_Formatter_Advanced();
        return $oAdvancedFormatter->format($event);
    }

    /**
     * After writing the log entry to the database, conditionally
     * send out a notification based on the notification rules.
     *
     * @param array $event the log event
     * @throws Zend_Log_Exception
     */
    protected function _write($event)
    {
        /** @var $event FireGento_Logger_Model_Event */
        //preformat the message
        $hostname = gethostname() !== false ? gethostname() : '';
        $event->setMessage(
            '[' . $hostname . '] ' . $event->getMessage()
        );

        if ($this->_db === null) {
            throw new Zend_Log_Exception('Database adapter is null');
        }

        if ($this->_columnMap === null) {
            $dataToInsert = $event;
        } else {
            $dataToInsert = array();
            foreach ($this->_columnMap as $columnName => $fieldKey) {
                $dataToInsert[$columnName] = $event->getDataUsingMethod($fieldKey);
            }
            $dataToInsert['advanced_info'] = $this->getAdvancedInfo($event);
        }

        $dataToInsert['timestamp'] = Mage::getSingleton('core/date')->gmtDate();
        $this->_db->insert($this->_table, $dataToInsert);

        /** @var Varien_Db_Adapter_Pdo_Mysql $db */
        $db = $this->_db;
        $connection = $db->getConnection();
        $lastInsertId = $connection->lastInsertId();
        $loggerEntry = Mage::getModel('firegento_logger/db_entry')->load($lastInsertId);

        $notificationMap = Mage::helper('firegento_logger')->getEmailNotificationRules();
        foreach ($notificationMap as $rule) {
            if ($this->_matchRule($rule, $loggerEntry)) {
                $this->_sendNotification($rule, $loggerEntry);
            }
        }
    }

    /**
     * Does the rule match
     *
     * @param  array                           $rule        the rule to use
     * @param  FireGento_Logger_Model_Db_Entry $loggerEntry the entry
     * @return bool
     */
    protected function _matchRule($rule, $loggerEntry)
    {
        $pattern = $rule['pattern'];
        if ($rule['severity'] != 'default' && $loggerEntry->getSeverity() > $rule['severity']) {
            return false;
        }

        if (!$rule['pattern']) {
            return true;
        }

        $result = preg_match("/$pattern/i", $loggerEntry->getMessage());
        if ($result) {
            return true;
        }

        return false;
    }

    /**
     * Send notifcations.
     *
     * @param array                           $rule        the rule
     * @param FireGento_Logger_Model_Db_Entry $loggerEntry the db entry
     */
    protected function _sendNotification($rule, $loggerEntry)
    {
        /** @var Mage_Core_Model_Email_Template $template */
        $template  = Mage::getModel('core/email_template')
            ->loadDefault('firegento_logger_notification_email_template');

        $email = Mage::getStoreConfig('trans_email/ident_general/email');
        $name = Mage::getStoreConfig('trans_email/ident_general/name');

        $template->setData('sender_name', $name )
            ->setData('sender_email', $email);

        $variables = array(
            'loggerentry_url' =>
                Mage::getUrl('adminhtml/logger/view', array('loggerentry_id' => $loggerEntry->getId())),
            'loggerentry' => $loggerEntry
        );

        $recipientsCsv = $rule['email_list_csv'];
        $recipients = array_map('trim', explode(",", $recipientsCsv));
        $template->send($recipients, null, $variables);
    }
}
