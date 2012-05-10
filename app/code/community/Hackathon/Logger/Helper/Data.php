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

    const XML_PATH_PRIORITY = 'logger/general/priority';

    /**
     * @param string $path
     * @param null $storeId
     * @return mixed
     */
    public function getLoggerConfig($path, $storeId = NULL)
    {
        return Mage::getStoreConfig('logger/'.$path, $storeId);
    }

    /**
     * @param Zend_Log_Writer_Abstract $writer
     * @param null $configPath
     */
    public function addPriorityFilter(Zend_Log_Writer_Abstract $writer, $configPath = NULL)
    {
        $priority = NULL;
        if ($configPath) {
            $priority = Mage::getStoreConfig($configPath);
            if ($priority == 'default') {
                $priority = NULL;
            }
        }
        if ( ! $configPath || ! strlen($priority)) {
            $priority = Mage::getStoreConfig(self::XML_PATH_PRIORITY);
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
     */
    public function addEventMetadata(&$event, $notAvailable = null)
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
        $maxBacktraceLines = (int) $this->getLoggerConfig('general/max_backtrace_lines');
        $backtraceFrames = array();
        if (version_compare(PHP_VERSION, '5.3.6') < 0 ) {
            $debugBacktrace = debug_backtrace(FALSE);
        } else if (version_compare(PHP_VERSION, '5.4.0') < 0) {
            $debugBacktrace = debug_backtrace($maxBacktraceLines > 0 ? 0 : DEBUG_BACKTRACE_IGNORE_ARGS);
        } else {
            $debugBacktrace = debug_backtrace($maxBacktraceLines > 0 ? 0 : DEBUG_BACKTRACE_IGNORE_ARGS, $maxBacktraceLines + 5);
        }
        foreach($debugBacktrace as $frame)
        {
            if ($nextIsFirst) {
                if (isset($frame['file']) && isset($frame['line'])) {
                    $event['file'] = str_replace($basePath, '', $frame['file']);
                    $event['line'] = $frame['line'];
                    $nextIsFirst = FALSE;
                    if ($recordBacktrace && $maxBacktraceLines) {
                        $backtraceFrames[] = $frame;
                        continue;
                    } else {
                        break;
                    }
                }
                continue;
            }
            if ($recordBacktrace) {
                if (count($backtraceFrames) >= $maxBacktraceLines) {
                    break;
                }
                $backtraceFrames[] = $frame;
                continue;
            }
            if (isset($frame['type']) && $frame['type'] == '::' && $frame['class'] == 'Mage' && substr($frame['function'], 0, 3) == 'log') {
                $nextIsFirst = TRUE;
                $recordBacktrace = ($frame['function'] != 'logException');
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
                foreach($frame['args'] as $value) {
                    $args[] = (is_object($value)
                        ? get_class($value)
                        : ( is_array($value)
                            ? 'array('.count($value).')'
                            : ( is_string($value)
                                ? (strlen($value) > 30 ? "'".substr($value, 0, 27)."...'" : $value)
                                : gettype($value)."($value)"
                            )
                        )
                    );
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

        if ( ! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $event['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if ( ! empty($_SERVER['REMOTE_ADDR'])) {
            $event['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
        } else {
            $event['REMOTE_ADDR'] = $notAvailable;
        }
    }

}
