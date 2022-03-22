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

    protected $_isInitialized = false;

    public function __construct($fileName = NULL)
    {
        $this->_fileName = $fileName ? basename($fileName) : NULL;
    }

    /**
     * initialize sentry
     *
     * @return bool
     */
    public function init()
    {
        if (!$this->_isInitialized) {
            $helper             = Mage::helper('firegento_logger');
            $dsn                = $helper->getLoggerConfig('sentry/public_dsn');
            if (!$dsn) {
                return false;
            }
            require_once Mage::getBaseDir('lib') . DS . 'sentry' . DS .  'Autoloader.php';
            $options            = [
                'trace'       => $this->_enableBacktrace,
                'curl_method' => $helper->getLoggerConfig('sentry/curl_method'),
                'prefixes'    => [BP],
            ];
            if ($environment = trim($helper->getLoggerConfig('sentry/environment'))) {
                $options['environment'] = $environment;
            }
            \Sentry\init($options);
            $this->_isInitialized = true;
        }
        return $this->_isInitialized;
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
            Mage::helper('firegento_logger')->addEventMetadata($event, NULL, $this->_enableBacktrace);

            if (!$this->init()) {
                return;
            }

            /**
             * Get message priority
             */
            if ( ! isset($event['priority']) || $event['priority'] === Zend_Log::ERR ) {
                $this->_assumePriorityByMessage($event);
            }
            $priority = isset($event['priority']) ? $event['priority'] : 3;

            //
            // Add extra data and tags
            //
            $data = [
                'tags' => [
                    'target' => $this->_fileName,
                    'storeCode' => $event->getStoreCode() ?: 'unknown',
                    'requestId' => $event->getRequestId(),
                ],
                'extra' => [
                    'timeElapsed' => $event->getTimeElapsed(),
                ]
            ];
            if ($event->getAdminUserId()) $data['extra']['adminUserId'] = $event->getAdminUserId();
            if ($event->getAdminUserName()) $data['extra']['adminUserName'] = $event->getAdminUserName();

            if (class_exists('Mage')) {
                if (Mage::registry('logger_data_tags')) {
                    $data['tags'] = array_merge($data['tags'], Mage::registry('logger_data_tags'));
                }
                if (Mage::registry('logger_data_extra')) {
                    $data['extra'] = array_merge($data['extra'], Mage::registry('logger_data_extra'));
                }
            }

            if ($event->getException()) {
                $eventId = \Sentry\captureException($event->getException(), \Sentry\EventHint::fromArray($data));
            } else {
                $data['level'] = $this->_priorityToLevelMapping[$priority];

                // Make Raven error handler transparent
                $backtrace = $event->getBacktraceArray() ?: TRUE;
                if (is_array($backtrace) && count($backtrace) > 3) {
                    if (  $backtrace[0]['function'] == 'log'
                       && $backtrace[1]['function'] == 'mageCoreErrorHandler'
                        && isset($backtrace[2]['class'])
                        && $backtrace[2]['class'] == 'Raven_Breadcrumbs_ErrorHandler'
                    ) {
                        array_shift($backtrace);
                        array_shift($backtrace);
                    }
                }
                $data['stacktrace'] = $backtrace;

                $eventId = \Sentry\captureMessage(
                    $event['message'],
                    \Sentry\Severity::fromError($data['level']),
                    \Sentry\EventHint::fromArray($data)
                );
            }
            Mage::unregister('logger_sentry_last_event_id');
            Mage::register('logger_sentry_last_event_id', (string)$eventId);

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
        if (
            stripos($event['message'], "warn") === 0 ||
            stripos($event['message'], "user warn") === 0
        ) {
            $event['priority'] = 4;
        }
        else if (
            stripos($event['message'], "notice") === 0 ||
            stripos($event['message'], "user notice") === 0 ||
            stripos($event['message'], "strict notice") === 0 ||
            stripos($event['message'], "deprecated") === 0
        ) {
            $event['priority'] = 5;
        }

        return $this;
    }

}