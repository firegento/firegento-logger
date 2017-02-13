<?php

/**
 *
 * @category    Log Manager
 * @package     Wf_Logmanager
 * @author      Magecredit Team <hi@magecredit.com>
 */
class Wf_Logmanager_Block_Adminhtml_List extends Mage_Core_Block_Template
{
    /**
     * Local store of the list of modules
     * @var array
     */
    protected $_lists = array();
    
    public function _construct()
    {
        $this->gather();
        return parent::_construct();
    }

    /**
     * Inspiration for this code was from Alan Storm's module list extension. 
     * Be sure to check it out some time at http://alanstorm.com/magento_list_module
     * @return $this
     */
    public function gather()
    {       
        $config = Mage::getConfig();
        foreach($config->getNode('modules')->children() as $item)
        {
            $o = new Varien_Object();
            $o->setName($item->getName());
            $o->setActive((string)$item->active);
            $o->setCodePool((string)$item->codePool);

            $isLogEnabled = Mage::getSingleton('logmanager/manager')->isEnabled($item->getName());
            $o->setLogEnabled($isLogEnabled);
            
            //use same logic from Mage_Core_Model_Config::getModuleDir
            //but recreated here to allow for poorly configued modules
            $codePool   = $config->getModuleConfig($item->getName())->codePool;
            $dir        = $config->getOptions()->getCodeDir().DS.$codePool.DS.uc_words($item->getName(),DS);            
            $o->setPath($dir);          
            
            $exists = file_exists($o->getPath());
            $exists = $exists ? 'yes' : 'no';
            $o->setPathExists($exists);
            
            $exists = file_exists($o->getPath() . DS . 'etc'.DS.'config.xml');
            $exists = $exists ? 'yes' : 'no';
            $o->setConfigExists($exists);
            
            
            if(!array_key_exists($o->getCodePool(), $this->_lists))
            {
                $this->_lists[$o->getCodePool()] = array();
            }
            $this->_lists[$o->getCodePool()][] = $o->toArray();


        }
        
        return $this;
    }

    /**
     * @return array
     */
    public function getModules()
    {
        $modules = array_merge($this->_lists['local'], $this->_lists['community']);
        return $modules;
    }
}
