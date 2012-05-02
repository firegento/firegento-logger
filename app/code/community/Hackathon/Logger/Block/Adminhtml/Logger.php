<?php
/**
 * Created by JetBrains PhpStorm.
 * User: spies
 * Date: 01.04.12 (14 KW)
 * Time: 02:08
 * To change this template use File | Settings | File Templates.
 */
class Hackathon_Logger_Block_Adminhtml_Logger extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		parent::__construct();
		$this->_controller = 'adminhtml_logger';
		$this->_blockGroup = 'hackathon_logger';
		$this->_headerText = Mage::helper('hackathon_logger')->__('Database entries');
		$this->removeButton('add');
	}
}
