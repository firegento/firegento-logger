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
 * Logger Grid
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_Logger_Block_Adminhtml_Logger_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var Firegento_Logger_Helper_Data
     */
    protected $_helper;

    /**
     * Instantiate the helper
     */
    protected function _construct()
    {
        $this->_helper = Mage::helper('firegento_logger');
    }

    /**
     * Grid Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('logger_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare the grid collection with the database log entries
     *
     * @return Firegento_Logger_Block_Adminhtml_Logger_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('firegento_logger/db_entry')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare the grid columns
     *
     * @return Firegento_Logger_Block_Adminhtml_Logger_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => Mage::helper('firegento_logger')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'entity_id',
        ));

        $this->addColumn('message', array(
            'header' => Mage::helper('firegento_logger')->__('Message'),
            'align' => 'left',
            'index' => 'message',
        ));

        $this->addColumn('timestamp', array(
            'header' => Mage::helper('firegento_logger')->__('Timestamp'),
            'align' => 'left',
            'index' => 'timestamp',
        ));

        $this->addColumn('severity', array(
            'header' => Mage::helper('firegento_logger')->__('Log Level'),
            'align' => 'left',
            'index' => 'severity',
            'type' => 'options',
            'options' => $this->getSeverityOptions(),
            'frame_callback' => array($this, 'decorateSeverity')
        ));

        return parent::_prepareColumns();
    }

    /**
     * Add a severity css class to the value
     *
     * @param  mixed         $value Logging Value
     * @param  Varien_Object $row   DB Log Entry
     * @return string Decorated HTML
     */
    public function decorateSeverity($value, $row)
    {
        $class = '';
        switch ($row->getSeverity()) {
            case Zend_Log::EMERG:
            case Zend_Log::ALERT:
            case Zend_Log::CRIT:
            case Zend_Log::ERR:
                $class = 'grid-severity-critical';
                break;
            case Zend_Log::WARN:
            case Zend_Log::NOTICE:
            case Zend_Log::INFO:
            case Zend_Log::DEBUG:
                $class = 'grid-severity-minor';
                break;
            default:
                $class = 'grid-severity-critical';
        }

        return '<span class="' . $class . '"><span>' . $value . '</span></span>';
    }

    /**
     * Retrieve the severity options
     *
     * @return array
     */
    public function getSeverityOptions()
    {
        return array(
            Zend_Log::EMERG => $this->_helper->__('Emergency'),
            Zend_Log::ALERT => $this->_helper->__('Alert'),
            Zend_Log::CRIT => $this->_helper->__('Critical'),
            Zend_Log::ERR => $this->_helper->__('Error'),
            Zend_Log::WARN => $this->_helper->__('Warning'),
            Zend_Log::NOTICE => $this->_helper->__('Notice'),
            Zend_Log::INFO => $this->_helper->__('Info'),
            Zend_Log::DEBUG => $this->_helper->__('Debug'),
        );
    }
}
