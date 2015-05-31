<?php
/**
 * This file is part of a FireGento e.V. module.
 *
 * This FireGento e.V. module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
 *
 * This script is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 *
 * @category  FireGento
 * @package   FireGento_Logger
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2013 FireGento Team (http://www.firegento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
/**
 * Block for manage modules log output in the backend
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Block_Adminhtml_Logger_Manager extends Mage_Core_Block_Template
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
        foreach ($config->getNode('modules')->children() as $item) {
            $o = new Varien_Object();
            $o->setName($item->getName());
            $o->setActive((string)$item->active);
            $o->setCodePool((string)$item->codePool);
            $isLogEnabled = Mage::getSingleton('firegento_logger/manager')->isEnabled($item->getName());
            $o->setLogEnabled($isLogEnabled);
            //use same logic from Mage_Core_Model_Config::getModuleDir
            //but recreated here to allow for poorly configued modules
            $codePool = $config->getModuleConfig($item->getName())->codePool;
            $dir = $config->getOptions()->getCodeDir().DS.$codePool.DS.uc_words($item->getName(),DS);
            $o->setPath($dir);
            $exists = file_exists($o->getPath());
            $exists = $exists ? 'yes' : 'no';
            $o->setPathExists($exists);
            $exists = file_exists($o->getPath() . DS . 'etc'.DS.'config.xml');
            $exists = $exists ? 'yes' : 'no';
            $o->setConfigExists($exists);
            if(!array_key_exists($o->getCodePool(), $this->_lists)) {
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
