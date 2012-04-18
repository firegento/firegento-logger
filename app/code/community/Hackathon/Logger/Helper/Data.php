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
            if ($priority == 'default') {
                $priority = NULL;
            }
        }
        if ( ! $configPath || ! strlen($priority)) {
            $priority = Mage::getStoreConfig(self::XML_PATH_PRIORITY);
        }
        if ( $priority !== NULL && $priority != Zend_Log::WARN) {
            $writer->addFilter(new Zend_Log_Filter_Priority((int)$priority));
        }
    }

    /**
     * Add useful metadata to the event
     *
     * @param array $event
     * @param null|string $notAvailable
     */
    public function addEventMetadata(&$event, $notAvailable = null)
    {
        $event['file'] = $notAvailable;
        $event['line'] = $notAvailable;
        $event['store_code'] = Mage::app()->getStore()->getCode();
        $event['time_elapsed'] = sprintf('%ds', time() - $_SERVER['REQUEST_TIME']);

        // Find file and line where message originated from
        $nextIsFirst = FALSE;
        foreach(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS) as $frame) {
            if ($frame['type'] == '::' && $frame['class'] == 'Mage' && substr($frame['function'], 0, 3) == 'log') {
                $nextIsFirst = TRUE;
                continue;
            }
            if ($nextIsFirst) {
                $event['file'] = $frame['file'];
                $event['line'] = $frame['line'];
                break;
            }
        }

        foreach(array('REQUEST_METHOD', 'REQUEST_URI', 'HTTP_USER_AGENT') as $key) {
            if ( ! empty($_SERVER[$key])) {
                $event[$key] = $_SERVER[$key];
            } else {
                $event[$key] = $notAvailable;
            }
        }
        if ( ! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $event['REMOTE_IP'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if ( ! empty($_SERVER['REMOTE_IP'])) {
            $event['REMOTE_IP'] = $_SERVER['REMOTE_IP'];
        } else {
            $event['REMOTE_IP'] = $notAvailable;
        }
    }

}
