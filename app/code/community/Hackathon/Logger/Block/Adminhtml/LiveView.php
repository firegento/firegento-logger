<?php

class Hackathon_Logger_Block_Adminhtml_LiveView extends Mage_Core_Block_Template
{

    public function getLogFiles()
    {
        $logFiles = array();

        $path = Mage::getBaseDir('var');
        $logPath = $path . '/log';

        $d = dir($logPath);
        while (false !== ($entry = $d->read())) {
            if ( strstr($entry, '.log') ) {
                $logFiles[] = $entry;
            }
        }
        $d->close();

        return $logFiles;
    }

}