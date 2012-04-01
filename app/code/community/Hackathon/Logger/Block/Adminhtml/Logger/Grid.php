<?php
/**
 * Created by JetBrains PhpStorm.
 * User: spies
 * Date: 01.04.12 (14 KW)
 * Time: 02:10
 * To change this template use File | Settings | File Templates.
 */

class Hackathon_Logger_Block_Adminhtml_Logger_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('logger_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('hackathon_logger/db_entry')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
		$this->addColumn('id', array(
			'header'    => Mage::helper('hackathon_logger')->__('ID'),
			'align'     =>'right',
			'width'     => '50px',
			'index'     => 'entity_id',
		));

		$this->addColumn('severity', array(
			'header'    => Mage::helper('hackathon_logger')->__('Log Level'),
			'align'     =>'left',
			'index'     => 'severity',
		));

		$this->addColumn('message', array(
			'header'    => Mage::helper('hackathon_logger')->__('Message'),
			'align'     =>'left',
			'index'     => 'message',
		));

		$this->addColumn('timestamp', array(
			'header'    => Mage::helper('hackathon_logger')->__('Timestamp'),
			'align'     =>'left',
			'index'     => 'timestamp',
		));
        return parent::_prepareColumns();
    }
}