<?php

class Hackathon_Logger_Block_Adminhtml_Logger_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /** @var Hackathon_Logger_Helper_Data */
    protected $_helper;

    /**
     * return void
     */
    public function _construct()
    {
        $this->_helper = Mage::helper('hackathon_logger');
    }

    /**
     * return void
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
     * @return this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('hackathon_logger/db_entry')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => Mage::helper('hackathon_logger')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'entity_id',
        ));

        $this->addColumn('message', array(
            'header' => Mage::helper('hackathon_logger')->__('Message'),
            'align' => 'left',
            'index' => 'message',
        ));

        $this->addColumn('timestamp', array(
            'header' => Mage::helper('hackathon_logger')->__('Timestamp'),
            'align' => 'left',
            'index' => 'timestamp',
        ));

        $this->addColumn('severity', array(
            'header' => Mage::helper('hackathon_logger')->__('Log Level'),
            'align' => 'left',
            'index' => 'severity',
            'type' => 'options',
            'options' => $this->getSeverityOptions(),
            'frame_callback' => array($this, 'decorateSeverity')
        ));
        return parent::_prepareColumns();
    }

    /**
     * @param $value
     * @param $row
     * @param $column
     * @return string
     */
    public function decorateSeverity($value, $row, $column)
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