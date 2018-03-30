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
 * Model for Sentry logging
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 *
 * see: https://github.com/magento-hackathon/LoggerSentry
 */
class FireGento_Logger_Model_Sentry extends FireGento_Logger_Model_Abstract
{

    /**
     * @var Raven_Client
     */
    protected static $_ravenClient;

    protected $_priorityToLevelMapping = [
        0 /*Zend_Log::EMERG*/  => 'fatal',
        1 /*Zend_Log::ALERT*/  => 'fatal',
        2 /*Zend_Log::CRIT*/   => 'fatal',
        3 /*Zend_Log::ERR*/    => 'error',
        4 /*Zend_Log::WARN*/   => 'warning',
        5 /*Zend_Log::NOTICE*/ => 'info',
        6 /*Zend_Log::INFO*/   => 'info',
        7 /*Zend_Log::DEBUG*/  => 'debug',
    ];

    protected $_fileName;
    protected $_priorityFilter;

    public function __construct($fileName = NULL)
    {
        $this->_fileName = $fileName ? basename($fileName) : NULL;
        $this->_priorityFilter = (int)Mage::helper('firegento_logger')->getLoggerConfig('sentry/priority');
    }

    /**
     * Retrieve Raven_Client instance
     *
     * @return Raven_Client|null
     */
    public function getRavenClient()
    {
        return self::$_ravenClient;
    }

    /**
     * Create Raven_Client instance
     *
     * @return void
     * @throws Raven_Exception
     */
    public function initRavenClient()
    {
        if (is_null(self::$_ravenClient)) {
            require_once Mage::getBaseDir('lib') . DS . 'sentry' . DS . 'lib' . DS . 'Raven' . DS . 'Autoloader.php';
            Raven_Autoloader::register();
            $helper             = Mage::helper('firegento_logger');
            $dsn                = $helper->getLoggerConfig('sentry/dsn');
            $options            = [
                'trace'       => $this->_enableBacktrace,
                'curl_method' => $helper->getLoggerConfig('sentry/curl_method'),
            ];
            self::$_ravenClient = new Raven_Client($dsn, $options);
            self::$_ravenClient->setAppPath(dirname(BP));
            self::$_ravenClient->trace = TRUE;
            $error_handler = new Raven_ErrorHandler(self::$_ravenClient, false);
            $error_handler->registerShutdownFunction();
        }
    }

    /**
     * Write a message to the log
     *
     * Sentry has own build-in processing the logs.
     * Nothing to do here.
     *
     * @see FireGento_Logger_Model_Observer::actionPreDispatch()
     *
     * @param FireGento_Logger_Model_Event $event
     * @throws Zend_Log_Exception
     */
    protected function _write($event)
    {
        try {
            $this->initRavenClient();

            /**
             * Get message priority
             */
            if ( ! isset($event['priority'])) {
                $this->_assumePriorityByMessage($event);
            }
            $priority = isset($event['priority']) ? $event['priority'] : 3;

            // If priority is high enough, send to Sentry
            if ($priority > $this->_priorityFilter) {
                return;
            }

            //
            // Add extra data (not using addEventMetadata since it repeats a lot of work with Sentry)
            //
            if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
                $timeElapsed = (float) sprintf('%f', microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']);
            } else {
                $timeElapsed = (float) sprintf('%d', time() - $_SERVER['REQUEST_TIME']);
            }
            self::$_ravenClient->context->tags = [
                'target' => $this->_fileName
            ];
            self::$_ravenClient->context->extra = [
                'storeCode' => Mage::app()->getStore()->getCode(),
                'timeElapsed' => $timeElapsed,
            ];
            if (Mage::app()->getStore()->isAdmin() && isset($_SESSION)) {
                $session = Mage::getSingleton('admin/session');
                if ($session->isLoggedIn()) {
                    self::$_ravenClient->context->extra['adminUserId'] = $session->getUser()->getId();
                    self::$_ravenClient->context->extra['adminUserName'] = $session->getUser()->getName();
                }
            }

            if ($event->getException()) {
                self::$_ravenClient->captureException($event->getException());
            } else {
                // Make Raven error handler transparent
                $backtrace = $event->getBacktraceArray() ?: TRUE;
                if (is_array($backtrace) && count($backtrace) > 3) {
                    if (  $backtrace[0]['function'] == 'log'
                       && $backtrace[1]['function'] == 'mageCoreErrorHandler'
                        && $backtrace[2]['class'] == 'Raven_Breadcrumbs_ErrorHandler'
                    ) {
                        array_shift($backtrace);
                        array_shift($backtrace);
                    }
                }

                self::$_ravenClient->captureMessage(
                    $event['message'],
                    [],
                    $this->_priorityToLevelMapping[$priority],
                    $backtrace
                );
            }

        } catch (Exception $e) {
            throw new Zend_Log_Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Try to attach a priority # based on the error message string (since sometimes it is not specified)
     *
     * @param $event
     * @return $this
     */
    protected function _assumePriorityByMessage(&$event)
    {
        if (stripos($event['message'], "warn") === 0) {
            $event['priority'] = 4;
        }
        else if (stripos($event['message'], "notice") === 0) {
            $event['priority'] = 5;
        }

        return $this;
    }

}