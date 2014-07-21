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
 */
class FireGento_Logger_Model_Event extends Varien_Object
{

    private $_iTimestamp;
    private $_iPriority;
    private $_sPriorityName;
    private $_sMessage;
    private $_sFile;
    private $_iLine;
    private $_sBacktrace;
    private $_sStoreCode;
    private $_iTimeElapsed;
    private $_sRequestMethod;
    private $_sRequestUri;
    private $_sHttpUserAgent;
    private $_sRequestData;
    private $_sRemoteAddress;
    private $_sHostname;

    /**
     * Set the current hostname
     *
     * @param  string $sHostname the hostname
     *
     * @return $this
     */
    public function setHostname($sHostname)
    {
        $this->_sHostname = $sHostname;
        return $this;
    }

    /**
     * Get the current hostname
     *
     * @return string
     */
    public function getHostname()
    {
        return $this->_sHostname;
    }

    /**
     * Set the remote address
     *
     * @param  string $sRemoteAddress the remote address
     *
     * @return $this
     */
    public function setRemoteAddress($sRemoteAddress)
    {
        $this->_sRemoteAddress = $sRemoteAddress;
        return $this;
    }

    /**
     * Get the current remote address
     *
     * @return string
     */
    public function getRemoteAddress()
    {
        return $this->_sRemoteAddress;
    }

    /**
     * Set the Requestdata header.
     *
     * @param  string $sRequestData the request data
     *
     * @return $this
     */
    public function setRequestData($sRequestData)
    {
        $this->_sRequestData = $sRequestData;
        return $this;
    }

    /**
     * Get the request data
     *
     * @return string
     */
    public function getRequestData()
    {
        return $this->_sRequestData;
    }


    /**
     * Set the request Method
     *
     * @param  string $sRequestMethod the request mwethod
     *
     * @return $this
     */
    public function setRequestMethod($sRequestMethod)
    {
        $this->_sRequestMethod = $sRequestMethod;
        return $this;
    }

    /**
     * The request method
     *
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->_sRequestMethod;
    }

    /**
     * Sett the request uri.
     *
     * @param  string $sRequestUri the rquest uri
     *
     * @return $this
     */
    public function setRequestUri($sRequestUri)
    {
        $this->_sRequestUri = $sRequestUri;
        return $this;
    }

    /**
     * Get the request uri
     *
     * @return string
     */
    public function getRequestUri()
    {
        return $this->_sRequestUri;
    }

    /**
     * Set the user agent.
     *
     * @param  string $sHttpUserAgent the user agent
     *
     * @return $this
     */
    public function setHttpUserAgent($sHttpUserAgent)
    {
        $this->_sHttpUserAgent = $sHttpUserAgent;
        return $this;
    }

    /**
     * Get the user agent.
     *
     * @return string
     */
    public function getHttpUserAgent()
    {
        return $this->_sHttpUserAgent;
    }

    /**
     * Set the current working file.
     *
     * @param  string $sFile the current working file
     *
     * @return $this
     */
    public function setFile($sFile)
    {
        $this->_sFile = $sFile;
        return $this;
    }

    /**
     * Get the current working file.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->_sFile;
    }

    /**
     * Set the backtrace to log.
     *
     * @param  string $sBacktrace the current backtrace
     *
     * @return $this
     */
    public function setBacktrace($sBacktrace)
    {
        $this->_sBacktrace = $sBacktrace;
        return $this;
    }

    /**
     * Get the backtrace.
     *
     * @return string
     */
    public function getBacktrace()
    {
        return $this->_sBacktrace;
    }

    /**
     * Set the current line.
     *
     * @param  integer $iLine the current line.
     *
     * @return $this
     */
    public function setLine($iLine)
    {
        $this->_iLine = $iLine;
        return $this;
    }

    /**
     * Get the current line.
     *
     * @return integer
     */
    public function getLine()
    {
        return $this->_iLine;
    }

    /**
     * Set the current store code.
     *
     * @param  string $sStoreCode the store code.
     *
     * @return $this
     */
    public function setStoreCode($sStoreCode)
    {
        $this->_sStoreCode = $sStoreCode;
        return $this;
    }

    /**
     * Get the store code.
     *
     * @return string
     */
    public function getStoreCode()
    {
        return $this->_sStoreCode;
    }

    /**
     * Set time elapsed
     *
     * @param  integer $iTimeElapsed time elapsed
     *
     * @return $this
     */
    public function setTimeElapsed($iTimeElapsed)
    {
        $this->_iTimeElapsed = $iTimeElapsed;
        return $this;
    }

    /**
     * Get time elapsed.
     *
     * @return integer
     */
    public function getTimeElapsed()
    {
        return $this->_iTimeElapsed;
    }

    /**
     * Set the priority
     *
     * @param  integer $iPriority the priority
     *
     * @return $this
     */
    public function setPriority($iPriority)
    {
        $this->_iPriority = $iPriority;
        return $this;
    }

    /**
     * Get the priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->_iPriority;
    }

    /**
     * The the time stamp.
     *
     * @param  integer $iTimestamp the timestamp
     *
     * @return $this
     */
    public function setTimestamp($iTimestamp)
    {
        $this->_iTimestamp = $iTimestamp;
        return $this;
    }

    /**
     * Get the timestamp
     *
     * @return integer
     */
    public function getTimestamp()
    {
        return $this->_iTimestamp;
    }

    /**
     * Set the actual message
     *
     * @param  string $sMessage the message
     *
     * @return $this
     */
    public function setMessage($sMessage)
    {
        $this->_sMessage = $sMessage;
        return $this;
    }

    /**
     * Get the message.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_sMessage;
    }

    /**
     * Set the priority name
     *
     * @param  string $sPriorityName the name of the priority
     *
     * @return $this
     */
    public function setPriorityName($sPriorityName)
    {
        $this->_sPriorityName = $sPriorityName;
        return $this;
    }

    /**
     * Get the priority name
     *
     * @return string
     */
    public function getPriorityName()
    {
        return $this->_sPriorityName;
    }

    /**
     * Append content to the message
     *
     * @param  string $sMessage message to append.
     *
     * @return $this
     */
    public function addMessage($sMessage)
    {
        $this->_sMessage .= $sMessage . PHP_EOL;
        return $this;
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
}
