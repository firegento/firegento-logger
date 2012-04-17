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
    /**
     * @param string $path
     * @param null $storeId
     * @return mixed
     */
    public function getLoggerConfig($path, $storeId = NULL)
    {
        return Mage::getStoreConfig('logger/'.$path, $storeId);
    }
}
