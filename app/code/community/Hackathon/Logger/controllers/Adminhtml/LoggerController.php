<?php
class Hackathon_Logger_Adminhtml_LoggerController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/logger');
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getStoreConfigFlag('logger/db/viewer_enabled') && Mage::getSingleton('admin/session')->isAllowed('system/logger');
    }
}
