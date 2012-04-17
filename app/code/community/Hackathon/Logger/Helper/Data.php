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

    const XML_PATH_SEVERITY_LEVEL = 'logger/general/severity';

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
    public function addSeverityFilter(Zend_Log_Writer_Abstract $writer, $configPath = NULL)
    {
        if( ! $configPath) {
            $configPath = self::XML_PATH_SEVERITY_LEVEL;
        }
        $writer->addFilter(new Zend_Log_Filter_Priority(Mage::getStoreConfig($configPath)));
    }

}
