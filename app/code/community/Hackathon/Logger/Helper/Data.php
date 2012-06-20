<?php
/**
 * Created by JetBrains PhpStorm.
 * User: damian
 * Date: 31.03.12
 * Time: 15:09
 * To change this template use File | Settings | File Templates.
 */
class Hackathon_Logger_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_PATH_PRIORITY = 'general/priority';

    protected $_targetMap = NULL;

    /**
     * @param string $path
     * @return string
     */
    public function getLoggerConfig($path)
    {
        // This method doesn't depend on stores being loaded.
        return (string) Mage::getConfig()->getNode('default/logger/'.$path);
    }

    /**
     * Returns an array of targets mapped or NULL if there was an error or there is no map.
     * Keys are target codes, values are bool indicating if backtrace is enabled
     *
     * @param string $filename
     * @return null|array
     */
    public function getMappedTargets($filename)
    {
        if ($this->_targetMap === NULL) {
            $targetMap = $this->getLoggerConfig('general/target_map');
            if ($targetMap) {
                $this->_targetMap = @unserialize($targetMap);
            } else {
                $this->_targetMap = FALSE;
            }
        }
        if ( ! $this->_targetMap) {
            return NULL;
        }
        $targets = array();
        foreach($this->_targetMap as $map) {
            if (@preg_match('/^'.$map['pattern'].'$/', $filename)) {
                $targets[$map['target']] = (int) $map['backtrace'];
                if ((int)$map['stop_on_match']) {
                    break;
                }
            }
        }
        return $targets;
    }

    /**
     * @param Zend_Log_Writer_Abstract $writer
     * @param null $configPath
     */
    public function addPriorityFilter(Zend_Log_Writer_Abstract $writer, $configPath = NULL)
    {
        $priority = NULL;
        if ($configPath) {
            $priority = $this->getLoggerConfig($configPath);
            if ($priority == 'default') {
                $priority = NULL;
            }
        }
        if ( ! $configPath || ! strlen($priority)) {
            $priority = $this->getLoggerConfig(self::XML_PATH_PRIORITY);
        }
        if ( $priority !== NULL && $priority != Zend_Log::WARN) {
            $writer->addFilter(new Zend_Log_Filter_Priority((int)$priority));
        }
    }

    /**
     * Add useful metadata to the event
     *
     * @param array $event
     * @param null|string $notAvailable
     * @param bool $enableBacktrace
     */
    public function addEventMetadata(&$event, $notAvailable = null, $enableBacktrace = FALSE)
    {
        $event['file'] = $notAvailable;
        $event['line'] = $notAvailable;
        $event['backtrace'] = $notAvailable;
        $event['store_code'] = Mage::app()->getStore()->getCode();
        if ( isset($_SERVER['REQUEST_TIME_FLOAT'])) {
          $event['time_elapsed'] = sprintf('%f', microtime(TRUE) - $_SERVER['REQUEST_TIME_FLOAT']);
        } else {
          $event['time_elapsed'] = sprintf('%d', time() - $_SERVER['REQUEST_TIME']);
        }

        // Find file and line where message originated from and optionally get backtrace lines
        $basePath = dirname(Mage::getBaseDir()).'/'; // Up one level in case deployed with symlinks from parent directory
        $nextIsFirst = FALSE;                        // Skip backtrace frames until we reach Mage::log(Exception)
        $recordBacktrace = FALSE;
        $maxBacktraceLines = $enableBacktrace ? (int) $this->getLoggerConfig('general/max_backtrace_lines') : 0;
        $backtraceFrames = array();
        if (version_compare(PHP_VERSION, '5.3.6') < 0 ) {
            $debugBacktrace = debug_backtrace(FALSE);
        } else if (version_compare(PHP_VERSION, '5.4.0') < 0) {
            $debugBacktrace = debug_backtrace($maxBacktraceLines > 0 ? 0 : DEBUG_BACKTRACE_IGNORE_ARGS);
        } else {
            $debugBacktrace = debug_backtrace($maxBacktraceLines > 0 ? 0 : DEBUG_BACKTRACE_IGNORE_ARGS, $maxBacktraceLines + 10);
        }
        foreach($debugBacktrace as $frame)
        {
            if (($nextIsFirst && $frame['function'] == 'logException') ||
                (isset($frame['type']) && $frame['type'] == '::' && $frame['class'] == 'Mage' && substr($frame['function'], 0, 3) == 'log')
            ) {
                if (isset($frame['file']) && isset($frame['line'])) {
                    $event['file'] = str_replace($basePath, '', $frame['file']);
                    $event['line'] = $frame['line'];
                    if ($maxBacktraceLines) {
                        $backtraceFrames = array();
                    } else if ($nextIsFirst) {
                        break;
                    } else {
                        continue;
                    }
                }
                if ($frame['function'] == 'logException') { // Don't record backtrace for Mage::logException
                    break;
                }
                $nextIsFirst = TRUE;
                $recordBacktrace = TRUE;
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
                if (empty($frame['file'])) $frame['file'] = 'unknown_file';
                else $frame['file'] = str_replace($basePath, '', $frame['file']);
                if (empty($frame['line'])) $frame['line'] = 0;
                $function = (isset($frame['class']) ? "{$frame['class']}{$frame['type']}":'').$frame['function'];
                $args = array();
                if (isset($frame['args'])) {
                    foreach($frame['args'] as $value) {
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
            $event['backtrace'] = implode("\n", $backtrace);
        }

        foreach(array('REQUEST_METHOD', 'REQUEST_URI', 'HTTP_USER_AGENT') as $key) {
            if ( ! empty($_SERVER[$key])) {
                $event[$key] = $_SERVER[$key];
            } else {
                $event[$key] = $notAvailable;
            }
        }

        if ($event['REQUEST_METHOD'] == $notAvailable) {
            $event['REQUEST_METHOD'] = php_sapi_name();
        }
        if ($event['REQUEST_URI'] == $notAvailable && isset($_SERVER['PHP_SELF'])) {
            $event['REQUEST_URI'] = $_SERVER['PHP_SELF'];
        }
        $requestData = array();
        if ( ! empty($_GET)) $requestData[] = '  GET|'.substr(@json_encode($_GET), 0, 1000);
        if ( ! empty($_POST)) $requestData[] = '  POST|'.substr(@json_encode($_POST), 0, 1000);
        if ( ! empty($_FILES)) $requestData[] = '  FILES|'.substr(@json_encode($_FILES), 0, 1000);
        $event['REQUEST_DATA'] = $requestData ? implode("\n", $requestData) : $notAvailable;


        if ( ! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $event['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if ( ! empty($_SERVER['REMOTE_ADDR'])) {
            $event['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
        } else {
            $event['REMOTE_ADDR'] = $notAvailable;
        }
    }

}
