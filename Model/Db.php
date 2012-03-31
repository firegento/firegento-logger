<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Christoph
 * Date: 31.03.12
 * Time: 20:17
 * To change this template use File | Settings | File Templates.
 */
class Hackathon_Logger_Model_Mail extends Zend_Log_Writer_Db
{
    /**
     * Database adapter instance
     * @var Zend_Db_Adapter
     */
    private $_db;

    /**
     * Name of the log table in the database
     * @var string
     */
    private $_table;

    /**
     * Relates database columns names to log data field keys.
     *
     * @var null|array
     */
    private $_columnMap;

    public function __construct($db, $table, $columnMap = null)
    {
        $resource = Mage::getSingleton('core/resource');
        $this->_db = $resource->getConnection('core_write');
        $this->_table = $resource->getTableName('hackathon_logger/logger');
        $this->_columnMap = array('severtiy' => 'priority', 'message' => 'message');
        parent::__construct($this->_db, $this->_table, $this->_columnMap);
    }

}