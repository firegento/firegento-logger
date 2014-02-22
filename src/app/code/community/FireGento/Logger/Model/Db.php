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
     * @var Zend_Db_Adapter Database adapter instance
     */
    private $_db;

    /**
     * @var string Name of the log table in the database
     */
    private $_table;

    /**
     * Relates database columns names to log data field keys.
     *
     * @var null|array
     */
    private $_columnMap;

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
     * Satisfy newer Zend Framework
     *
     * @param  array|Zend_Config $config Configuration
     * @return void|Zend_Log_FactoryInterface
     */
    public static function factory($config)
    {

    }

    /**
     * After writing the log entry to the database, conditionally
     * send out a notification based on the notification rules.
     *
     * @param array $event
     */
    protected function _write($event)
    {
        //preformat the message
        $hostname = gethostname() !== false ? gethostname() : '';
        $event->setMessage(
            '[' . $hostname . '] ' . $event->getMessage()
        );

        if ($this->_db === null) {
            #require_once 'Zend/Log/Exception.php';
            throw new Zend_Log_Exception('Database adapter is null');
        }

        if ($this->_columnMap === null) {
            $dataToInsert = $event;
        } else {
            $dataToInsert = array();
            foreach ($this->_columnMap as $columnName => $fieldKey) {
                $dataToInsert[$columnName] = $event->getDataUsingMethod($fieldKey);
            }
        }

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
     * @param $rule
     * @param $loggerEntry FireGento_Logger_Model_Db_Entry
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
     * @param $rule
     * @param $loggerEntry FireGento_Logger_Model_Db_Entry
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
