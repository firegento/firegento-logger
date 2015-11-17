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

    /**
     * @var null
     */
    protected $_targetMap = null;

    /**
     * @var null
     */
    protected $_notificationRules = null;

    /**
     * Get logger config value
     *
     * @param  string $path Config Path
     * @return string Config Value
     */
    public function getLoggerConfig($path)
    {
        return (string) Mage::getConfig()->getNode('default/logger/'.$path);
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
            if ($targetMap) {
                $this->_targetMap = @unserialize($targetMap);
            } else {
                $this->_targetMap = false;
            }
        }
        if (! $this->_targetMap) {
            return null;
        }
        $targets = array();
        foreach ($this->_targetMap as $map) {
            if (@preg_match('/^'.$map['pattern'].'$/', $filename)) {
                $targets[$map['target']] = (int) $map['backtrace'];
                if ((int) $map['stop_on_match']) {
                    break;
                }
            }
        }
        return $targets;
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
        if ($priority !== null && $priority != Zend_Log::WARN) {
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
        $event
            ->setFile($notAvailable)
            ->setLine($notAvailable)
            ->setBacktrace($notAvailable)
            ->setStoreCode(Mage::app()->getStore()->getCode());

        // Add request time
        if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            $event->setTimeElapsed((float) sprintf('%f', microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']));
        } else {
            $event->setTimeElapsed((float) sprintf('%d', time() - $_SERVER['REQUEST_TIME']));
        }

        // Find file and line where message originated from and optionally get backtrace lines
        $basePath = dirname(Mage::getBaseDir()).'/'; // 1 level up in case deployed with symlinks from parent directory
        $nextIsFirst = false;                        // Skip backtrace frames until we reach Mage::log(Exception)
        $recordBacktrace = false;
        $maxBacktraceLines = $enableBacktrace ? (int) $this->getLoggerConfig('general/max_backtrace_lines') : 0;
        $backtraceFrames = array();
        if (version_compare(PHP_VERSION, '5.3.6') < 0 ) {
            $debugBacktrace = debug_backtrace(false);
        } elseif (version_compare(PHP_VERSION, '5.4.0') < 0) {
            $debugBacktrace = debug_backtrace(
                $maxBacktraceLines > 0 ? 0 : DEBUG_BACKTRACE_IGNORE_ARGS
            );
        } else {
            $debugBacktrace = debug_backtrace(
                $maxBacktraceLines > 0 ? 0 : DEBUG_BACKTRACE_IGNORE_ARGS,
                $maxBacktraceLines + 10
            );
        }

        foreach ($debugBacktrace as $frame) {
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
                    if ($maxBacktraceLines) {
                        $backtraceFrames = array();
                    } elseif ($nextIsFirst) {
                        break;
                    } else {
                        continue;
                    }
                }

                // Don't record backtrace for Mage::logException
                if ($frame['function'] == 'logException') {
                    break;
                }

                $nextIsFirst = true;
                $recordBacktrace = true;
                continue;
            }

            if ($recordBacktrace) {
                if (count($backtraceFrames) >= $maxBacktraceLines) {
                    break;
                }
                $backtraceFrames[] = $frame;
                continue;
            }
        }

        if ($backtraceFrames) {
            $backtrace = array();
            foreach ($backtraceFrames as $index => $frame) {
                // Set file
                if (empty($frame['file'])) {
                    $frame['file'] = 'unknown_file';
                } else {
                    $frame['file'] = str_replace($basePath, '', $frame['file']);
                }

                // Set line
                if (empty($frame['line'])) {
                    $frame['line'] = 0;
                }

                $function = (isset($frame['class']) ? "{$frame['class']}{$frame['type']}":'').$frame['function'];
                $args = array();
                if (isset($frame['args'])) {
                    foreach ($frame['args'] as $value) {
                        $args[] = (is_object($value)
                            ? get_class($value)
                            : ( is_array($value)
                                ? 'array('.count($value).')'
                                : ( is_string($value)
                                    ? "'".(strlen($value) > 28 ? "'".substr($value, 0, 25)."...'" : $value)."'"
                                    : gettype($value)."($value)"
                                )
                            )
                        );
                    }
                }

                $args = implode(', ', $args);
                $backtrace[] = "#{$index} {$frame['file']}:{$frame['line']} $function($args)";
            }

            $event->setBacktrace(implode("\n", $backtrace));
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

        // Fetch request data
        $requestData = array();
        if (!empty($_GET)) {
            $requestData[] = '  GET|'.substr(@json_encode($this->filterSensibleData($_GET)), 0, 1000);
        }
        if (!empty($_POST)) {
            $requestData[] = '  POST|'.substr(@json_encode($this->filterSensibleData($_POST)), 0, 1000);
        }
        if (!empty($_FILES)) {
            $requestData[] = '  FILES|'.substr(@json_encode($_FILES), 0, 1000);
        }
        if (Mage::registry('raw_post_data')) {
            $requestData[] = '  RAWPOST|'.substr(Mage::registry('raw_post_data'), 0, 1000);
        }
        $event->setRequestData($requestData ? implode("\n", $requestData) : $notAvailable);

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
            $keysToFilter = explode("\n",
                Mage::helper('firegento_logger')->getLoggerConfig('general/filter_request_data'));
            foreach ($keysToFilter as $key) {
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
