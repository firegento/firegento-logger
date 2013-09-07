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

    public function liveViewAjaxAction()
    {
        $startPos = $this->getRequest()->getParam('position');
        $filename = Mage::getBaseDir('var') . DS . 'log' . DS . $this->getRequest()->getParam('logFile');
        if( !file_exists($filename) ){
            return '';
        }

        $handle = fopen($filename, 'r');
        $filesize = filesize($filename);

        if($startPos == 0) {
            $lengthBefore = 1000;
            fseek($handle, -$lengthBefore, SEEK_END);
            $text = fread($handle, $filesize);

            $updates = '[...]' . substr($text, strpos($text, "\n"), strlen($text));
        } else {
            fseek($handle, $startPos, SEEK_SET);
            $updates = fread($handle, $filesize);
        }
        $newPos = ftell($handle);

        $response = '';
        if($updates != NULL) {
            $response = Zend_Json::encode(array('text' => $updates, 'position' => $newPos));
        }

        $this->getResponse()->setBody(
            $response
        );
    }

    public function reportAjaxAction()
    {
        $reportId = $this->getRequest()->getParam('report_id');
        $filename = Mage::getBaseDir('var') . DS . 'report' . DS . $reportId;

        if(!file_exists($filename)){
            return '';
        }

        $handle = fopen($filename, 'r');
        $filesize = filesize($filename);

        $text = fread($handle, $filesize);

        $this->getResponse()->setBody(
            Zend_Json::encode(array('text' => $text))
        );
    }

    protected function _isAllowed()
    {
        return Mage::getStoreConfigFlag('logger/db/viewer_enabled') && Mage::getSingleton('admin/session')->isAllowed('system/logger');
    }
}
