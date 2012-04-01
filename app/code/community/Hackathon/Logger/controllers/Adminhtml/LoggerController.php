<?php
class Hackathon_Logger_Adminhtml_LoggerController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
		$this->loadLayout();
		$this->renderLayout();
    }
}
