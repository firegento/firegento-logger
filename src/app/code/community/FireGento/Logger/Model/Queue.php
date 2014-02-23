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
 * PHP version 5
 *
 * @category  FireGento
 * @package   FireGento_Logger
 * @author    FireGento Team <team@firegento.com>
 * @copyright 2013 FireGento Team (http://www.firegento.com)
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
/**
 * This writer is the one actually used by Magento. It acts as a proxy to support one or more writers
 * set from the config and optionally as a "queue" to hold all events until shutdown.
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Queue extends Zend_Log_Writer_Abstract
{
    /**
     * @var Zend_Log_Writer_Abstract[]
     */
    protected $_writers = array();

    /**
     * @var array
     */
    private $_loggerCache = array();

    /**
     * @var bool
     */
    protected $_useQueue;

    /**
     * @var FireGento_Logger_Formatter_Advanced
     */
    protected static $_advancedFormatter;

    /**
     * Class constructor
     *
     * @param string $filename Filename
     */
    public function __construct($filename)
    {
        /** @var $helper FireGento_Logger_Helper_Data */
        $helper = Mage::helper('firegento_logger');;

        // Only instantiate writers that are needed for this file based on the Filename Filters
        $targets = explode(',', $helper->getLoggerConfig('general/targets'));
        if ($targets) {
            $mappedTargets = $helper->getMappedTargets(basename($filename));
            if ($mappedTargets === null) { // No filters, enable backtrace for all targets
                $mappedTargets = array_fill_keys($targets, true);
            } else {
                $targets = array_intersect($targets, array_keys($mappedTargets));
            }
            //writer intstantiation
            foreach ($targets as $target) {
                $class = (string) Mage::app()->getConfig()->getNode('global/log/core/writer_models/'.$target.'/class');
                if ($class) {
                    $writer = new $class($filename);
                    //add filter to target
                    $helper->addPriorityFilter($writer, $target.'/priority');
                    //add backtrace if you need if support is enabled
                    if (method_exists($writer, 'setEnableBacktrace')) {
                        $writer->setEnableBacktrace($mappedTargets[$target]);
                    }
                    $this->_writers[] = $writer;
                }
            }
        }

        $this->_useQueue = (boolean) $helper->getLoggerConfig('general/use_queue');

    }

    /**
     * Write a message to the log.
     *
     * @param array $event log data event
     */
    protected function _write($event)
    {
        /** @var $event FireGento_Logger_Model_Event */
        $event = Mage::helper('firegento_logger')->getEventObjectFromArray($event);

        if ($this->_useQueue) {
            // if queue is enabled then add to internal cache
            $this->_loggerCache[] = $event;
        } else {
            foreach ($this->_writers as $writer) {
                $writer->write($event);
            }
        }
    }

    /**
     * At the end of the request we write to the actual logger
     */
    public function shutdown()
    {
        foreach ($this->_writers as $writer) {
            //only implode if queue is enabled and cache has entries
            if ($this->_useQueue && count($this->_loggerCache) > 0) {
                $writer->write($this->implodeEvents($this->_loggerCache));
            }
            $writer->shutdown();
        }
    }

    /**
     * Generate one big event out of queued events.
     *
     * @param  array $events all queued events
     *
     * @return array
     */
    public function implodeEvents($events)
    {
        /** @var $bigEvent FireGento_Logger_Model_Event */
        $bigEvent = Mage::getModel('firegento_logger/event');
        $bigEvent->setPriority(0)->setMessage('');

        foreach ($events as $event) {
            /** @var FireGento_Logger_Model_Event $event */
            if ($bigEvent->getPriority() > $event->getPriority()) {
                $bigEvent
                    ->setPriority($event->getPriority())
                    ->setPriorityName($event->getPriorityName())
                    ->setTimestamp($event->getTimestamp());
            }
            $bigEvent->addMessage($event->getMessage());
        }
        return $bigEvent;
    }

    /**
     * Override this method since Mage::log doesn't let us set a formatter any other way.
     *
     * @param Zend_Log_Formatter_Interface $formatter Formatter
     */
    public function setFormatter(Zend_Log_Formatter_Interface $formatter)
    {
        $this->_formatter = self::getAdvancedFormatter();
        foreach ($this->_writers as $writer) {
            if (get_class($writer) == 'Zend_Log_Writer_Stream') { // don't override formatter for default writer
                $writer->setFormatter($formatter);
            } else {
                $writer->setFormatter(self::getAdvancedFormatter());
            }
        }
    }

    /**
     * Returns the advanced formatter
     *
     * @return FireGento_Logger_Formatter_Advanced
     */
    public static function getAdvancedFormatter()
    {
        // Use singleton since all instances will be identical anyway
        if (!self::$_advancedFormatter) {
            self::$_advancedFormatter = new FireGento_Logger_Formatter_Advanced;
        }

        return self::$_advancedFormatter;
    }

    /**
     * Satisfy newer Zend Framework
     *
     * @param  array|Zend_Config $config Configuration
     * @return void|Zend_Log_FactoryInterface
     */
    public static function factory($config)
    {

    }
}
