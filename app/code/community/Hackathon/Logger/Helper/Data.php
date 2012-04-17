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

    const XML_PATH_PRIORITY = 'logger/general/priority';

    /**
     * @param string $path
     * @param null $storeId
     * @return mixed
     */
    public function getLoggerConfig($path, $storeId = NULL)
    {
        return Mage::getStoreConfig('logger/'.$path, $storeId);
    }

    /**
     * @param Zend_Log_Writer_Abstract $writer
     * @param null $configPath
     */
    public function addPriorityFilter(Zend_Log_Writer_Abstract $writer, $configPath = NULL)
    {
        $priority = NULL;
        if ($configPath) {
            $priority = Mage::getStoreConfig($configPath);
        }
        if ( ! $configPath || ! strlen($priority)) {
            $priority = Mage::getStoreConfig(self::XML_PATH_PRIORITY);
        }
        if ( $priority !== NULL && $priority != Zend_Log::WARN) {
            $writer->addFilter(new Zend_Log_Filter_Priority((int)$priority));
        }
    }

}
