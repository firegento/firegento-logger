<?php

/**
 * Hackathon Logger Model for the Chrome Logger.
 *
 * This model makes it possible to log magento messages via the
 * ChromeLogger Plugin at Google Chrome Browser.
 *
 * You must include chromelogger library for php under lib.
 *
 * @see http://craig.is/writing/chrome-logger
 *
 * @copyright SYNAXON AG
 * @package   Hackathon_Logger
 * @author    Daniel Kröger <daniel.kroeger@synaxon.de>
 * @version   26.07.2013
 */

class Hackathon_Logger_Model_Chromelogger extends Zend_Log_Writer_Abstract
{
    /**
     * @param $event
     */
    public function _write($event)
    {
        $priority = array_key_exists('priority', $event) ? $event['priority'] : false;
        $message  = $this->_formatter->format($event);

        if ($priority !== false)
        {
            switch($priority)
            {
                case Zend_Log::EMERG:
                case Zend_Log::ALERT:
                case Zend_Log::CRIT:
                case Zend_Log::ERR:
                    Hackathon_Logger_Model_Chromelogger_Library_ChromePhp::error($message);
                    break;
                case Zend_Log::WARN:
                    Hackathon_Logger_Model_Chromelogger_Library_ChromePhp::warn($message);
                    break;
                case Zend_Log::NOTICE:
                case Zend_Log::INFO:
                case Zend_Log::DEBUG:
                    Hackathon_Logger_Model_Chromelogger_Library_ChromePhp::info($message);
                    break;
                default:
                    Mage::log('Unknown loglevel at ' . __CLASS__);
                    break;
            }
        }
        else
        {
            Mage::log('Attached message event has no priority - skipping !');
        }
    }

    /**
     * Satisfy newer Zend Framework
     *
     * @static
     * @param $config
     */
    static public function factory($config) {}

}
