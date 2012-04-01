<?php
class Hackathon_Logger_Adminhtml_LoggerController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        Mage::log("Hello", Zend_Log::CRIT);
        Mage::log("Hello2", Zend_Log::CRIT);

        $this->loadLayout();
		$this->renderLayout();
    }

    public function testAction(){
        exit();
    }
}
