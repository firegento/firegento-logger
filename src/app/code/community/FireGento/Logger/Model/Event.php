<?php

class FireGento_Logger_Model_Event extends Mage_Core_Model_Abstract
{

    private $iTimestamp,
        $iPriority,
        $sPriorityName,
        $sMessage,
        $sFile,
        $iLine,
        $sBacktrace,
        $sStoreCode,
        $iTimeElapsed,
        $sRequestMethod,
        $sRequestUri,
        $sHttpUserAgent,
        $sRequestData,
        $sRemoteAddress,
        $sHostname;

    /**
     * @param string $sHostname
     * @return $this
     */
    public function setHostname($sHostname)
    {
        $this->sHostname = $sHostname;
        return $this;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->sHostname;
    }

    /**
     * @param string $sRemoteAddress
     * @return $this
     */
    public function setRemoteAddress($sRemoteAddress)
    {
        $this->sRemoteAddress = $sRemoteAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getRemoteAddress()
    {
        return $this->sRemoteAddress;
    }

    /**
     * @param string $sRequestData
     * @return $this
     */
    public function setRequestData($sRequestData)
    {
        $this->sRequestData = $sRequestData;
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestData()
    {
        return $this->sRequestData;
    }


    /**
     * @param string $sRequestMethod
     * @return $this
     */
    public function setRequestMethod($sRequestMethod)
    {
        $this->sRequestMethod = $sRequestMethod;
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->sRequestMethod;
    }

    /**
     * @param string $sRequestUri
     * @return $this
     */
    public function setRequestUri($sRequestUri)
    {
        $this->sRequestUri = $sRequestUri;
        return $this;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return $this->sRequestUri;
    }

    /**
     * @param string $sHttpUserAgent
     * @return $this
     */
    public function setHttpUserAgent($sHttpUserAgent)
    {
        $this->sHttpUserAgent = $sHttpUserAgent;
        return $this;
    }

    /**
     * @return string
     */
    public function getHttpUserAgent()
    {
        return $this->sHttpUserAgent;
    }

    /**
     * @param string $sFile
     * @return $this
     */
    public function setFile($sFile)
    {
        $this->sFile = $sFile;
        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->sFile;
    }

    /**
     * @param string $sBacktrace
     * @return $this
     */
    public function setBacktrace($sBacktrace)
    {
        $this->sBacktrace = $sBacktrace;
        return $this;
    }

    /**
     * @return string
     */
    public function getBacktrace()
    {
        return $this->sBacktrace;
    }

    /**
     * @param integer $iLine
     * @return $this
     */
    public function setLine($iLine)
    {
        $this->iLine = $iLine;
        return $this;
    }

    /**
     * @return integer
     */
    public function getLine()
    {
        return $this->iLine;
    }

    /**
     * @param string $sStoreCode
     * @return $this
     */
    public function setStoreCode($sStoreCode)
    {
        $this->sStoreCode = $sStoreCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getStoreCode()
    {
        return $this->sStoreCode;
    }

    /**
     * @param integer $iTimeElapsed
     * @return $this
     */
    public function setTimeElapsed($iTimeElapsed)
    {
        $this->iTimeElapsed = $iTimeElapsed;
        return $this;
    }

    /**
     * @return integer
     */
    public function getTimeElapsed()
    {
        return $this->iTimeElapsed;
    }

    /**
     * @param integer $iPriority
     * @return $this
     */
    public function setPriority($iPriority)
    {
        $this->iPriority = $iPriority;
        return $this;
    }

    /**
     * @return integer
     */
    public function getPriority()
    {
        return $this->iPriority;
    }

    /**
     * @param integer $iTimestamp
     * @return $this
     */
    public function setTimestamp($iTimestamp)
    {
        $this->iTimestamp = $iTimestamp;
        return $this;
    }

    /**
     * @return integer
     */
    public function getTimestamp()
    {
        return $this->iTimestamp;
    }

    /**
     * @param string $sMessage
     * @return $this
     */
    public function setMessage($sMessage)
    {
        $this->sMessage = $sMessage;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->sMessage;
    }

    /**
     * @param string $sPriorityName
     * @return $this
     */
    public function setPriorityName($sPriorityName)
    {
        $this->sPriorityName = $sPriorityName;
        return $this;
    }

    /**
     * @return string
     */
    public function getPriorityName()
    {
        return $this->sPriorityName;
    }

    /**
     * @param $sMessage
     * @return $this
     */
    public function addMessage($sMessage)
    {
        $this->sMessage .= $sMessage . PHP_EOL;
        return $this;
    }

    /**
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