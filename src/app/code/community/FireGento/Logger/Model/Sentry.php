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

use Sentry\EventHint;
use Sentry\Severity;
use function Sentry\captureException;
use function Sentry\captureMessage;

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

    protected static $_isInitialized = false;

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
        if (!self::$_isInitialized) {
            $helper             = Mage::helper('firegento_logger');
            $dsn                = $helper->getLoggerConfig('sentry/public_dsn');
            if (!$dsn) {
                return false;
            }
            require_once Mage::getBaseDir('lib') . DS . 'sentry' . DS .  'Autoloader.php';
            Sentry_Autoloader::register();

            //The options
            // "curl_method",
            // "trace" do not exist. Defined options are: "attach_stacktrace", "before_breadcrumb", "before_send", "capture_silenced_errors", "class_serializers", "context_lines", "default_integrations", "dsn", "enable_compression", "environment", "error_types", "http_proxy", "in_app_exclude", "in_app_include", "integrations", "logger", "max_breadcrumbs", "max_request_body_size", "max_value_length", "prefixes", "release", "sample_rate", "send_attempts", "send_default_pii", "server_name", "tags", "traces_sample_rate", "traces_sampler".
            $options            = [
                'dsn' => \Sentry\Dsn::createFromString($dsn),
                'attach_stacktrace' => $this->_enableBacktrace,
                'prefixes'    => [BP],
            ];
            if ($environment = trim($helper->getLoggerConfig('sentry/environment'))) {
                $options['environment'] = $environment;
            }
            \Sentry\init($options);
            self::$_isInitialized = true;
        }
        return self::$_isInitialized;
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
                ],
                'attach_stacktrace' => true
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
                $eventId = captureException($event->getException(), EventHint::fromArray($data));
            } else {
                $level = $this->_priorityToLevelMapping[$priority];

                $eventId = captureMessage(
                    $event['message'],
                    $this->_getSeverityFromLevel($level),
                    EventHint::fromArray($data)
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
            $event->setPriority(4);
        }
        else if (
            stripos($event['message'], "notice") === 0 ||
            stripos($event['message'], "user notice") === 0 ||
            stripos($event['message'], "strict notice") === 0 ||
            stripos($event['message'], "deprecated") === 0
        ) {
            $event->setPriority(5);
        }

        return $this;
    }

    protected function _getSeverityFromLevel(string $level): Severity
    {
        switch ($level) {
            case 'fatal':
                return Severity::fatal();
            case 'error':
                return Severity::error();
            case 'warning':
                return Severity::warning();
            case 'info':
                return Severity::info();
            case 'debug':
            default:
                return Severity::debug();
        }
    }

}