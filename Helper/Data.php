<?php
/**
 * Created by JetBrains PhpStorm.
 * User: damian
 * Date: 31.03.12
 * Time: 15:09
 * To change this template use File | Settings | File Templates.
 */
class Hackathon_Logger_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getLoggerConfig($value){
        return Mage::getStoreConfig(''.$value,Mage::app()->getStore()->getId());
    }
}
