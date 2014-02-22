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
     * Preformat the message
     *
     * @param array $event the actual log event
     */
    protected function _write($event)
    {
        $hostname = gethostname() !== false ? gethostname() : '';
        $event->setMessage(
            '[' . $hostname . '] ' . $event->getMessage()
        );
        parent::_write($event);
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
}
