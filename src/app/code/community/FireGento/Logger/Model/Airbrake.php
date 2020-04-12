<?php
/**
 * This file is part of a FireGento e.V. module.
 * This FireGento e.V. module is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License version 3 as
 * published by the Free Software Foundation.
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
 * Class FireGento_Logger_Model_Airbrake
 *
 * This file was ported from Elgentos_CodebaseExceptions_Helper_Data
 * into this logger module.
 *
 * https://github.com/airbrake/Airbrake-Magento/blob/master/app/code/community/Elgentos/CodebaseExceptions/Helper/Data.php
 *
 */


require_once Mage::getBaseDir('lib') . DS . 'Airbrake' . DS . 'Client.php';
require_once Mage::getBaseDir('lib') . DS . 'Airbrake' . DS . 'Configuration.php';

class FireGento_Logger_Model_Airbrake extends Zend_Log_Writer_Abstract
{

    protected $_apiKey;
    
    public function __construct()
    {
        $helper = Mage::helper('firegento_logger');
        $this->_apiKey = $helper->getLoggerConfig('airbrake/apikey');

        if ($this->isDisabled()) {
            return;
        }


        $options = array();
        // REQUEST_URI is not available in the CLI context
        if (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = explode("/", $_SERVER['REQUEST_URI']);
            $options['action'] = array_pop($requestUri);
            $options['component'] = implode('/', array_slice($requestUri, -2));
        } else {
            $options['action'] = $_SERVER['PHP_SELF'];
            $options['component'] = $_SERVER['PHP_SELF'];
        }

        $projectRoot = explode('/', $_SERVER['PHP_SELF']);
        array_pop($projectRoot);
        $options['projectRoot'] = implode('/', $projectRoot) . '/';
        $options['host'] = $helper->getLoggerConfig('airbrake/host');
        $options['secure'] = $helper->getLoggerConfig('airbrake/secure');
        $options['environmentName'] = $helper->getLoggerConfig('airbrake/environment');
        $options['timeout'] = $helper->getLoggerConfig('airbrake/timeout');
        $config = new Airbrake\Configuration($this->_apiKey, $options);
        $this->client = new Airbrake\Client($config);
    }


    /**
     * Write a message to the log.
     *
     * @param  array $event event data
     * @return void
     * @throws Zend_Log_Exception
     */
    protected function _write($event)
    {
        $this->sendToAirbrake($event['message'], 4);
    }

    protected function isDisabled()
    {
        if (strlen(trim($this->_apiKey)) == 0) {
            return true;
        }
        return false;
    }

    public function insertException($reportData)
    {
        if ($this->isDisabled()) {
            return;
        }
        $backtraceLines = explode("\n", $reportData[1]);
        $backtraces = $this->formatStackTraceArray($backtraceLines);

        $this->client->notifyOnError($reportData[0], $backtraces);
    }

    /**
     * @param string $message
     * @param int $backtraceLinesToSkip Number of backtrace lines/frames to skip
     */
    public function sendToAirbrake($message, $backtraceLinesToSkip = 1)
    {
        if ($this->isDisabled()) {
            return;
        }

        $message = trim($message);
        $messageArray = explode("\n", $message);
        if (empty($messageArray)) {
            return;
        }
        $errorClass = 'PHP Error';
        $errorMessage = array_shift($messageArray);
        $backTrace = array_slice(debug_backtrace(), $backtraceLinesToSkip);

        $matches = array();
        if (preg_match('/exception \'(.*)\' with message \'(.*)\' in .*/', $errorMessage, $matches)) {
            $errorMessage = $matches[2];
            $errorClass = $matches[1];
        }
        if (count($messageArray) > 0) {
            $errorMessage .= '... [truncated]';
        }

        $notice = new \Airbrake\Notice;
        $notice->load(
            array(
                'errorClass' => $errorClass,
                'backtrace' => $backTrace,
                'errorMessage' => $errorMessage,
            )
        );

        $this->client->notify($notice);
    }

    /**
     * @param array $backtraceLines
     * @return array
     */
    protected function formatStackTraceArray($backtraceLines)
    {
        $backtraces = array();

        foreach ($backtraceLines as $backtrace) {
            $temp = array();
            $parts = explode(': ', $backtrace);

            if (isset($parts[1])) {
                $temp['function'] = $parts[1];
            }

            $temp['file'] = substr($parts[0], 0, stripos($parts[0], '('));
            $temp['line'] = substr(
                $parts[0], stripos($parts[0], '(') + 1, (stripos($parts[0], ')') - 1) - stripos($parts[0], '(')
            );

            if (!empty($temp['function'])) {
                $backtraces[] = $temp;
            }
        }
        return $backtraces;
    }

    /**
     * Satisfy newer Zend Framework
     *
     * @param  array|Zend_Config $config Configuration
     *
     * @return void|Zend_Log_FactoryInterface
     */
    public static function factory($config)
    {

    }
}
