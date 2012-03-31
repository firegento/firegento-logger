<?php
/**
 * Created by JetBrains PhpStorm.
 * User: damian
 * Date: 31.03.12
 * Time: 15:36
 * To change this template use File | Settings | File Templates.
 */
class Hackathon_Logger_Model_System_Config_Source_Targets
{
    public function toOptionArray()
    {
        $helper = Mage::helper('hackathon_logger');
        return array(
            array('label' => $helper->__('Mail'), 'value' => 'mail'),
            array('label' => $helper->__('DB'), 'value' => 'db'),
            array('label' => $helper->__('XMPP'), 'value' => 'xmpp'),
        );
    }
}