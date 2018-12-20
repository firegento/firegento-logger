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
 * General Event Model to abstract from Zend API.
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 *
 * @method string getRequestId()
 * @method $this setRequestId(string $value)
 * @method string getHostname()
 * @method $this setHostname(string $value)
 * @method string getRemoteAddress()
 * @method $this setRemoteAddress(string $value)
 * @method string getRequestData()
 * @method $this setRequestData(string $value)
 * @method string getRequestMethod()
 * @method $this setRequestMethod(string $value)
 * @method string getRequestUri()
 * @method $this setRequestUri(string $value)
 * @method string getStoreCode()
 * @method $this setStoreCode(string $value)
 * @method int getAdminUserId()
 * @method $this setAdminUserId(int $value)
 * @method string getAdminUserName()
 * @method $this setAdminUserName(string $value)
 * @method string getHttpUserAgent()
 * @method $this setHttpUserAgent(string $value)
 * @method string getHttpCookie()
 * @method $this setHttpCookie(string $value)
 * @method string getHttpHost()
 * @method $this setHttpHost(string $value)
 * @method string getFile()
 * @method $this setFile(string $value)
 * @method $this setBacktrace(string $value)
 * @method array getBacktraceArray()
 * @method $this setBacktraceArray(array $value)
 * @method Exception getException()
 * @method $this setException(Exception $value)
 * @method string getMessage()
 * @method $this setMessage(string $value)
 * @method string getPriorityName()
 * @method $this setPriorityName(string $value)
 * @method int getLine()
 * @method $this setLine(int $value)
 * @method float getTimeElapsed()
 * @method $this setTimeElapsed(float $value)
 * @method int getPriority()
 * @method $this setPriority(int $value)
 * @method int getTimestamp()
 * @method $this setTimestamp(int $value)
 * @method string getSessionData()
 * @method $this setSessionData(string $value)
 *
 */
class FireGento_Logger_Model_Event extends Varien_Object implements ArrayAccess
{
    /**
     * Append content to the message
     *
     * @param  string $sMessage message to append.
     *
     * @return $this
     */
    public function addMessage($sMessage)
    {
        return $this->setMessage($this->getMessage() . $sMessage . PHP_EOL);
    }

    /**
     * Only convert backtrace array to string if target actually uses this property
     *
     * @return string
     */
    public function getBacktrace()
    {
        if ($this->_getData('backtrace') === TRUE) {
            if ($this->getBacktraceArray()) {
                $basePath = dirname(Mage::getBaseDir()).'/'; // 1 level up in case deployed with symlinks from parent directory
                $backtrace = array();
                foreach ($this->getBacktraceArray() as $index => $frame) {
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
                                        ? "'".(strlen($value) > 100 ? "'".substr($value, 0, 100)."...'" : $value)."'"
                                        : gettype($value)."($value)"
                                    )
                                )
                            );
                        }
                    }

                    $args = implode(', ', $args);
                    $backtrace[] = "#{$index} {$frame['file']}:{$frame['line']} $function($args)";
                }
                $this->setBacktrace(implode("\n", $backtrace));
            } else {
                $this->setBacktrace('-');
            }
        }
        return $this->_getData('backtrace');
    }

    /**
     * Get the current data in an array
     *
     * @return array
     */
    public function getEventDataArraySimple()
    {
        return array(
            'timestamp' => $this->getTimestamp(),
            'priority' => $this->getPriority(),
            'priorityName' => $this->getPriorityName(),
            'message' => $this->getMessage(),
        );
    }

    /**
     * Get the current data in an array
     *
     * @return array
     */
    public function getEventDataArray()
    {
        return array(
            'timestamp' => $this->getTimestamp(),
            'priority' => $this->getPriority(),
            'priorityName' => $this->getPriorityName(),
            'message' => $this->getMessage(),
            'requestId' => $this->getRequestId(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'backtrace' => $this->getBacktrace(),
            'storeCode' => $this->getStoreCode(),
            'timeElapsed' => $this->getTimeElapsed(),
            'requestMethod' => $this->getRequestMethod(),
            'requestUri' => $this->getRequestUri(),
            'httpUserAgent' => $this->getHttpUserAgent(),
            'httpHost' => $this->getHttpHost(),
            'httpCookie' => $this->getHttpCookie(),
            'sessionData' => $this->getSessionData(),
            'requestData' => $this->getRequestData(),
            'remoteAddress' => $this->getRemoteAddress(),
            'hostname' => $this->getHostname(),
        );
    }

    public function offsetSet($offset, $value) {
        throw new Mage_Core_Exception('Log Event assignment not implemented');
    }

    public function offsetExists($offset) {
        $offset = $this->_underscore($offset);
        return isset($this->_data[$offset]);
    }

    public function offsetUnset($offset) {
        throw new Mage_Core_Exception('Log Event assignment not implemented');
    }

    public function offsetGet($offset) {
        if ($offset == 'backtrace') {
            return $this->getBacktrace();
        }
        $offset = $this->_underscore($offset);
        return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
    }
}
