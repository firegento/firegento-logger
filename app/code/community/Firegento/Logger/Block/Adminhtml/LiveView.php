<?php

class Firegento_Logger_Block_Adminhtml_LiveView extends Mage_Adminhtml_Block_Template
{
    /**
     * Method reads all log-Files in the var/log-folder
     *
     * @return array
     */
    public function getLogFiles()
    {
        $logFiles = array();

        $directory = new DirectoryIterator(Mage::getBaseDir('var') . DS . 'log');
        foreach ($directory as $fileInfo) {
            if (!$fileInfo->isFile() || !preg_match('/\.(?:log)$/', $fileInfo->getFilename())) {
                continue;
            }

            $logFiles[] = $fileInfo->getFilename();
        }

        return $logFiles;
    }

}
