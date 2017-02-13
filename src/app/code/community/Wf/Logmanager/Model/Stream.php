<?php
/**
 *
 * @category    Log Manager
 * @package     Wf_Logmanager
 * @author      Magecredit Team <hi@magecredit.com>
 */
class Wf_Logmanager_Model_Stream extends Zend_Log_Writer_Stream
{
    /**
     * Write a message to the log.
     *
     * @param  array  $event  event data
     * @return void
     */
    protected function _write($event)
    {
        $backtrace = debug_backtrace();
        array_shift($backtrace);
        array_shift($backtrace);
        array_shift($backtrace);
        $file = $backtrace[0]['file'];

        $moduleDir = $file;

        // The way this works is it sifts backwards through the log to find which module called this log.
        $codeStart = stripos($file, DS.'code'.DS);
        $moduleDir = substr($moduleDir, $codeStart +strlen(DS.'code'.DS));
        $moduleDir = str_ireplace('community' . DS, '', $moduleDir);
        $moduleDir = str_ireplace('local' . DS, '', $moduleDir);
        
        $endIndex = stripos($moduleDir, DS, stripos($moduleDir, DS)+1);
        $moduleKey = str_replace(DS, "_", substr($moduleDir, 0, $endIndex));
        
        if(!Mage::getSingleton('logmanager/manager')->isEnabled($moduleKey)) {
            return $this;
        }

        return parent::_write($event);
    }
}
