<?php

class Hackathon_Logger_Block_Adminhtml_LiveView extends Mage_Adminhtml_Block_Template
{
    public function getLogFiles()
    {
        $logFiles = array();

        $directory = new DirectoryIterator(Mage::getBaseDir('var') . DS . 'log');
        foreach($directory as $fileInfo)
        {
            if(!$fileInfo->isFile() || !preg_match('/\.(?:log)$/', $fileInfo->getFilename())) {
                continue;
            }

            $logFiles[] = $fileInfo->getFilename();
        }

        return $logFiles;
    }

}