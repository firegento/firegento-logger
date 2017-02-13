<?php

class Wf_Logmanager_Adminhtml_ManagelogController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Lists the modules and gives the user the option to dsiable/enable log output for them.
     * @return $this
     */
    public function indexAction()
    {

        $this->_title($this->__('Module Log Manager'))->_title($this->__('Module Log Manager'));

        $this->loadLayout();

        $this->_setActiveMenu('system/config/logmanager');

        $listBlock = $this->getLayout()->createBlock('logmanager/adminhtml_list', 'logmanager_list')->setTemplate('wf/logmanager/list.phtml');
        $this->_addContent(
            $listBlock
        );

        $this->renderLayout();
    }

    /**
     * Disables povided module key's log output
     * @return $this
     */
    public function disableAction()
    {
        $moduleKey = $this->getRequest()->getParam('module');

        Mage::getSingleton('logmanager/manager')->disableLogging($moduleKey);

        $successMsg = Mage::helper('logmanager')->__("Logging for the module '%s' was successfully DISABLED.", $moduleKey);
        Mage::getSingleton('core/session')->addSuccess($successMsg);

        $this->_redirect("adminhtml/managelog/index");

        return $this;
    }


    /**
     * Disables povided module key's log output
     * @return $this
     */
    public function enableAction()
    {
        $moduleKey = $this->getRequest()->getParam('module');

        Mage::getSingleton('logmanager/manager')->enableLogging($moduleKey);

        $successMsg = Mage::helper('logmanager')->__("Logging for the module '%s' was successfully ENABLED.", $moduleKey);
        Mage::getSingleton('core/session')->addSuccess($successMsg);

        $this->_redirect("adminhtml/managelog/index");

        return $this;
    }

    /**
     * Test function to see if log restrictions are working. Try restricting log for the Wf_Logmanager module.
     * @return $this
     */
    public function testlogAction()
    {
        Mage::log("This is a test from the Wf_Logmanager module.");

        die("Check logs now...");

        return $this;
    }

    /**
     * If they're not allowed to acces the config then they're probably not allowed to access this section.
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config');
    }

}
