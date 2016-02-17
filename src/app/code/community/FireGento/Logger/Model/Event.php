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
 * @method string getHttpUserAgent()
 * @method $this setHttpUserAgent(string $value)
 * @method string getFile()
 * @method $this setFile(string $value)
 * @method string getBacktrace()
 * @method $this setBacktrace(string $value)
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
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'backtrace' => $this->getBacktrace(),
            'storeCode' => $this->getStoreCode(),
            'timeElapsed' => $this->getTimeElapsed(),
            'requestMethod' => $this->getRequestMethod(),
            'requestUri' => $this->getRequestUri(),
            'httpUserAgent' => $this->getHttpUserAgent(),
            'requestData' => $this->getRequestData(),
            'remoteAddress' => $this->getRemoteAddress(),
            'hostname' => $this->getHostname(),
        );
    }

    public function offsetSet($offset, $value) {
        throw new Mage_Core_Exception('Log Event assignment not implemented');
    }

    public function offsetExists($offset) {
        $data = $this->getEventDataArray();
        return isset($data[$offset]);
    }

    public function offsetUnset($offset) {
        throw new Mage_Core_Exception('Log Event assignment not implemented');
    }

    public function offsetGet($offset) {
        $data = $this->getEventDataArray();
        return isset($data[$offset]) ? $data[$offset] : null;
    }
}
