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
 * Log Manager
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Manager extends Varien_Object
{
    /**
     * This is a local store of configured disabled log modules
     * @var null
     */
    protected $_cfg = null;

    /**
     * Provide an extension key (ie TBT_Rewards) to disable the logging output from that module.
     * @param  string $moduleKey
     * @return $this
     */
    public function disableLogging($moduleKey)
    {
        return $this->toggleLogging($moduleKey, false);
    }

    /**
     * Provide an extension key (ie TBT_Rewards) to enable the logging output from that module.
     * @param  string $moduleKey
     * @return $this
     */
    public function enableLogging($moduleKey)
    {
        return $this->toggleLogging($moduleKey, true);
    }

    /**
     * Provide an extension key (ie TBT_Rewards) to disable the logging output from that module.
     * @param  string $moduleKey
     * @param  bool $disable if true, log output will be disabled for the specified module.
     * @return $this
     */
    public function toggleLogging($moduleKey, $disable)
    {
        $oldCfg = $this->_getCfg();
        $key = array_search($moduleKey, $oldCfg);
        if ($disable) {
            if ($key !== false) {
                unset($oldCfg[$key]);
            }
        } else {
            if ($key === false) {
                $oldCfg[] = $moduleKey;
            }
        }
        $newCfg = implode(",", $oldCfg);
        $cfg = new Mage_Core_Model_Config();
        $cfg ->saveConfig('dev/log/disabled_modules', $newCfg, 'default', 0);
        return $this;
    }


    /**
     * Tells you if an extension's log output is currently enabled
     * @param  string  $moduleKey
     * @return boolean
     */
    public function isEnabled($moduleKey)
    {
        $cfg = $this->_getCfg();
        $key = array_search($moduleKey, $cfg);
        if ($key !== false) {
            return false;
        }
        return true;
    }

    /**
     * Get the local store of config values
     * @return array
     */
    protected function _getCfg()
    {
        if ($this->_cfg != null) {
            return $this->_cfg;
        }
        $cfg = Mage::getStoreConfig('dev/log/disabled_modules');
        if (empty($cfg)) {
            $this->_cfg = array();
        } else {
            $this->_cfg = explode(",", $cfg);
        }
        return $this->_cfg;
    }
}
