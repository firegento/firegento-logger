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
 * Helper Class
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_PRIORITY = 'general/priority';
    const XML_PATH_MAX_DAYS = 'db/max_days_to_keep';

    protected $_targets;
    protected $_targetMap;
    protected $_targetsForFilename = [];
    protected $_notificationRules;
    protected $_maxBacktraceLines;
    protected $_maxDataLength;
    protected $_prettyPrint;
    protected $_addSessionData;
    protected $_keysToFilter;

    public function __construct()
    {
        $this->_maxBacktraceLines = (int) $this->getLoggerConfig('general/max_backtrace_lines');
        $this->_maxDataLength = $this->getLoggerConfig('general/max_data_length') ?: 1000;
        $this->_prettyPrint = $this->getLoggerConfig('general/pretty_print') && defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0;
        $this->_addSessionData = $this->getLoggerConfig('general/add_session_data');
        $this->_keysToFilter = explode("\n", $this->getLoggerConfig('general/filter_request_data'));
    }

    /**
     * Return a random id for this request
     */
    public function getRequestId()
    {
        if ( ! Mage::registry('logger_request_id')) {
            try {
                $requestId = preg_replace('/\W/','',base64_encode(random_bytes(6)));
                Mage::register('logger_request_id', $requestId);
            } catch (Exception $e) {}
        }
        return Mage::registry('logger_request_id');
    }

    /**
     * Get logger config value
     *
     * @param  string $path Config Path
     * @return string Config Value
     */
    public function getLoggerConfig($path)
    {
        // Do not use Mage::getStoreConfig so that logger may work when db config is not loaded
        return (string) Mage::getConfig()->getNode('default/logger/'.$path);
    }

    /**
     * @return array
     */
    public function getAllTargets()
    {
        if ($this->_targets === NULL) {
            $this->_targets = explode(',', $this->getLoggerConfig('general/targets'));
        }
        return $this->_targets;
    }

    /**
     * Returns an array of targets mapped or null if there was an error or there is no map.
     * Keys are target codes, values are bool indicating if backtrace is enabled
     *
     * @param  string $filename Filename
     * @return null|array Mapped Targets
     */
    public function getMappedTargets($filename)
    {
        if ($this->_targetMap === null) {
            $targetMap = $this->getLoggerConfig('general/target_map');
            if ($targetMap && ($targetMap = @unserialize($targetMap))) {
                $this->_targetMap = $targetMap;
            } else {
                $this->_targetMap = [];
            }
        }
        if ( ! isset($this->_targetsForFilename[$filename])) {
            $targets = array();
            foreach ($this->_targetMap as $map) {
                if (@preg_match('/^'.str_replace('/', '\\/', $map['pattern']).'$/', $filename)) {
                    $targets[$map['target']] = (int) $map['backtrace'];
                    if ((int) $map['stop_on_match']) {
                        break;
                    }
                }
            }
            $this->_targetsForFilename[$filename] = $targets;
        }
        return $this->_targetsForFilename[$filename];
    }

    /**
     * The maximun of days to keep log messages in the database table.
     *
     * @return string Days to keep
     */
    public function getMaxDaysToKeep()
    {
        return $this->getLoggerConfig(self::XML_PATH_MAX_DAYS);
    }

    /**
     * Add priority filte to writer instance
     *
     * @param Zend_Log_Writer_Abstract $writer     Writer Instance
     * @param null|string              $configPath Config Path
     */
    public function addPriorityFilter(Zend_Log_Writer_Abstract $writer, $configPath = null)
    {
        $priority = null;
        if ($configPath) {
            $priority = $this->getLoggerConfig($configPath);
            if ($priority == 'default') {
                $priority = null;
            }
        }
        if ( ! $configPath || ! strlen($priority)) {
            $priority = $this->getLoggerConfig(self::XML_PATH_PRIORITY);
        }
        if ($priority !== null) {
            $writer->addFilter(new Zend_Log_Filter_Priority((int) $priority));
        }
    }

    /**
     * Add useful metadata to the event
     *
     * @param FireGento_Logger_Model_Event &$event          Event Data
     * @param null|string                  $notAvailable    Not available
     * @param bool                         $enableBacktrace Flag for Backtrace
     */
    public function addEventMetadata(&$event, $notAvailable = null, $enableBacktrace = false)
    {
        $event->setBacktrace($enableBacktrace ? TRUE : $notAvailable);

        // Only add metadata once even if there are multiple targets
        if ($event->getStoreCode()) {
            return;
        }

        $event
            ->setRequestId($this->getRequestId())
            ->setFile($notAvailable)
            ->setLine($notAvailable)
            ->setStoreCode(Mage::app()->getStore()->getCode());

        // Add admin user data
        if (Mage::app()->getStore()->isAdmin() && isset($_SESSION) && isset($_SESSION['admin'])) {
            $session = Mage::getSingleton('admin/session');
            if ($session->isLoggedIn()) {
                $event->setAdminUserId($session->getUser()->getId());
                $event->setAdminUserName($session->getUser()->getName());
            }
        }

        // Add request time
        if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            $event->setTimeElapsed((float) sprintf('%f', microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']));
        } else {
            $event->setTimeElapsed((float) sprintf('%d', time() - $_SERVER['REQUEST_TIME']));
        }

        // Add backtrace data as array only for now, populate 'file' and 'line'
        if ( ! $event->getBacktraceArray()) {
            // Find file and line where message originated from and optionally get backtrace lines
            $basePath = dirname(Mage::getBaseDir()).'/'; // 1 level up in case deployed with symlinks from parent directory
            $nextIsFirst = false;                        // Skip backtrace frames until we reach Mage::log(Exception)
            $recordBacktrace = false;

            $backtraceFrames = array();
            if (version_compare(PHP_VERSION, '5.4.0') < 0) {
                $debugBacktrace = debug_backtrace();
            } else {
                $debugBacktrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT,
                    $this->_maxBacktraceLines + 10
                );
            }
            array_shift($debugBacktrace); // FireGento_Logger_Helper_Data::addEventMetadata
            array_shift($debugBacktrace); // FireGento_Logger_Model_*::_write
            array_shift($debugBacktrace); // Zend_Log_Write_Abstract::write
            array_shift($debugBacktrace); // FireGento_Logger_Model_Queue::_write
            array_shift($debugBacktrace); // Zend_Log_Writer_Abstract::write
            array_shift($debugBacktrace); // Zend_Log::log
            foreach ($debugBacktrace as $frame) {
                if (isset($frame['class']) && $frame['class'] == 'Zend_Log' && $frame['function'] == 'log') {
                    array_shift($debugBacktrace);
                    continue;
                }
                if (($nextIsFirst && $frame['function'] == 'logException')
                    || (
                        isset($frame['type'])
                        && $frame['type'] == '::'
                        && $frame['class'] == 'Mage'
                        && substr($frame['function'], 0, 3) == 'log'
                    )
                ) {
                    if (isset($frame['file']) && isset($frame['line'])) {
                        $event
                            ->setFile(str_replace($basePath, '', $frame['file']))
                            ->setLine($frame['line']);
                        if ($this->_maxBacktraceLines) {
                            $backtraceFrames = array($frame);
                        } elseif ($nextIsFirst) {
                            break;
                        } else {
                            continue;
                        }
                    }

                    // Don't record backtrace for Mage::logException
                    if ($frame['function'] == 'logException') {
                        if (isset($frame['args'][0])) {
                            $event->setException($frame['args'][0]);
                        }
                        continue;
                    }

                    $nextIsFirst = true;
                    $recordBacktrace = true;
                    continue;
                }

                if ($recordBacktrace) {
                    if (count($backtraceFrames) >= $this->_maxBacktraceLines) {
                        break;
                    }

                    // Avoid exposing passwords in backtrace
                    if (preg_match('/^(login|authenticate|setPassword|validatePassword)$/', $frame['function'])) {
                        foreach ($frame['args'] as &$arg) {
                            if (is_string($arg)) {
                                $arg = '**redacted**';
                            }
                        }
                    }

                    $backtraceFrames[] = $frame;
                }
            }
            $event->setBacktraceArray($backtraceFrames);
        }

        if (!empty($_SERVER['REQUEST_METHOD'])) {
            $event->setRequestMethod($_SERVER['REQUEST_METHOD']);
        } else {
            $event->setRequestMethod(php_sapi_name());
        }

        if (!empty($_SERVER['REQUEST_URI'])) {
            $event->setRequestMethod($_SERVER['REQUEST_URI']);
        } else {
            $event->setRequestMethod($_SERVER['PHP_SELF']);
        }

        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $event->setHttpUserAgent($_SERVER['HTTP_USER_AGENT']);
        }

        if (!empty($_SERVER['HTTP_HOST'])) {
            $event->setHttpHost($_SERVER['HTTP_HOST']);
        }

        if (!empty($_SERVER['HTTP_COOKIE'])) {
            $event->setHttpCookie($_SERVER['HTTP_COOKIE']);
        }

        // Fetch request data
        $requestData = array();
        if (!empty($_GET)) {
            $requestData[] = '  GET|'.substr(@json_encode($this->filterSensibleData($_GET), $this->_prettyPrint), 0, $this->_maxDataLength);
        }
        if (!empty($_POST)) {
            $requestData[] = '  POST|'.substr(@json_encode($this->filterSensibleData($_POST), $this->_prettyPrint), 0, $this->_maxDataLength);
        }
        if (!empty($_FILES)) {
            $requestData[] = '  FILES|'.substr(@json_encode($_FILES, $this->_prettyPrint), 0, $this->_maxDataLength);
        }
        if (Mage::registry('raw_post_data')) {
            $requestData[] = '  RAWPOST|'.substr(Mage::registry('raw_post_data'), 0, $this->_maxDataLength);
        }
        $event->setRequestData($requestData ? implode("\n", $requestData) : $notAvailable);

        // Add session data if enabled
        if ($this->_addSessionData) {
            $event->setSessionData(empty($_SESSION) ? $notAvailable : substr(@json_encode($_SESSION, $this->_prettyPrint), 0, $this->_maxDataLength));
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $event->setRemoteAddress($_SERVER['HTTP_X_FORWARDED_FOR']);
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $event->setRemoteAddress($_SERVER['REMOTE_ADDR']);
        } else {
            $event->setRemoteAddress($notAvailable);
        }

        // Add hostname to log message ...
        if (gethostname() !== false) {
            $event->setHostname(gethostname());
        } else {
            $event->setHostname('Could not determine hostname !');
        }
    }

    /**
     * filter sensible data like credit card and password from requests
     *
     * @param  array $data the data to be filtered
     * @return array
     */
    private function filterSensibleData($data)
    {
        if (is_array($data)) {
            foreach ($this->_keysToFilter as $key) {
                $key = trim($key);
                if ($key !== '') {
                    $subkeys = explode('.', $key);
                    $data = $this->filterDataFromMultidimensionalKey($data, $subkeys);
                }
            }
        }
        return $data;
    }

    /**
     * Filter the data.
     *
     * @param  array $data    array to be filtered
     * @param  array $subkeys list of multidimensional keys
     * @return array
     */
    private function filterDataFromMultidimensionalKey(array $data, array $subkeys)
    {
        $countSubkeys = count($subkeys);
        $lastSubkey = ($countSubkeys - 1);
        $subdata = &$data;
        for ($i = 0; $i < $lastSubkey; $i++) {
            if (isset($subdata[$subkeys[$i]])) {
                $subdata =  &$subdata[$subkeys[$i]];
            }
        }
        if (array_key_exists($subkeys[$lastSubkey], $subdata)) {
            $subdata[$subkeys[$lastSubkey]] = '*****';
        }
        return $data;
    }

    /**
     * Get all the notification rules.
     *
     * @return array|mixed|null an array of rules
     */
    public function getEmailNotificationRules()
    {
        if ($this->_notificationRules != null) {
            return $this->_notificationRules;
        }

        $notificationRulesSerialized = $this->getLoggerConfig('db/email_notification_rule');
        if (! $notificationRulesSerialized) {
            return array();
        }
        $notificationRules = unserialize($notificationRulesSerialized);

        $this->_notificationRules = $notificationRules;
        return $notificationRules;
    }

    /**
     * Convert Array to Event Object
     *
     * @param  array $event Event
     *
     * @return FireGento_Logger_Model_Event
     */
    public function getEventObjectFromArray($event)
    {
        // if more than one logger is active the first logger convert the array
        if (is_object($event) && get_class($event) == get_class(Mage::getModel('firegento_logger/event'))) {
            return $event;
        }
        return Mage::getModel('firegento_logger/event')
            ->setTimestamp($event['timestamp'])
            ->setMessage($event['message'])
            ->setPriority($event['priority'])
            ->setPriorityName($event['priorityName']);
    }

}
