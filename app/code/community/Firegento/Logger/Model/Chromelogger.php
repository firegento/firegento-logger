<?php
class Firegento_Logger_Model_Chromelogger extends Zend_Log_Writer_Abstract
{
    /**
     * @param $event
     */
    public function _write($event)
    {
        $priority = array_key_exists('priority', $event) ? $event['priority'] : false;
        $message  = $this->_formatter->format($event);

        if ($priority !== false) {
            switch ($priority) {
                case Zend_Log::EMERG:
                case Zend_Log::ALERT:
                case Zend_Log::CRIT:
                case Zend_Log::ERR:
                    Firegento_Logger_Model_Chromelogger_Library_ChromePhp::error($message);
                    break;
                case Zend_Log::WARN:
                    Firegento_Logger_Model_Chromelogger_Library_ChromePhp::warn($message);
                    break;
                case Zend_Log::NOTICE:
                case Zend_Log::INFO:
                case Zend_Log::DEBUG:
                    Firegento_Logger_Model_Chromelogger_Library_ChromePhp::info($message);
                    break;
                default:
                    Mage::log('Unknown loglevel at ' . __CLASS__);
                    break;
            }
        } else {
            Mage::log('Attached message event has no priority - skipping !');
        }
    }

    /**
     * Satisfy newer Zend Framework
     *
     * @static
     * @param $config
     */
    public static function factory($config) {}

}
