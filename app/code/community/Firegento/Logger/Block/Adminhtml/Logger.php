<?php

class Firegento_Logger_Block_Adminhtml_Logger extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_controller = 'adminhtml_logger';
		$this->_blockGroup = 'firegento_logger';
		$this->_headerText = Mage::helper('firegento_logger')->__('Database entries');
		$this->removeButton('add');
	}
}
