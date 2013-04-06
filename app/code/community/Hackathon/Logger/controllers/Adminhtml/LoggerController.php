<?php
class Hackathon_Logger_Adminhtml_LoggerController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/hackathon_logger/grid_viewer');
        $this->renderLayout();
    }

    public function liveViewAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/hackathon_logger/live_viewer');
        $this->renderLayout();
    }

    public function reportViewAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/hackathon_logger/report_viewer');
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return Mage::getStoreConfigFlag('logger/db/viewer_enabled') && Mage::getSingleton('admin/session')->isAllowed('system/logger');
    }
}
