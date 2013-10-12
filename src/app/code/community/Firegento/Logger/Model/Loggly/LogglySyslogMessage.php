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
 * Implementation of Remote Syslog Message for Loggly. This class logs the
 * events using JSON, which allows providing more details than basic Syslog.
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class Firegento_Logger_Model_Loggly_LogglySyslogMessage extends SyslogMessage
{
    /**
     * Class constructor
     *
     * @param string $message   Message to log
     * @param int    $facility  Facility
     * @param int    $severity  Severity
     * @param int    $timestamp Timestamp
     * @param null   $options   Options
     */
    public function __construct($message, $facility = 16, $severity = 5, $timestamp, $options = null)
    {
        parent::__construct($message, $facility, $severity, $timestamp, $options);
    }

    /**
     * Puts all Log Message elements together to form a JSON String that will be
     * passed to the RSysLog Server.
     *
     * @return string The Message as a JSON object.
     */
    protected function FormatMessage()
    {
        // @codingStandardsIgnoreStart
        $this->Message['FQDN'] = $this->GetFQDN();
        $this->Message['ProcessName'] = $this->GetProcessName();
        $this->Message['PID'] = getmypid();
        return json_encode($this->Message);
        // @codingStandardsIgnoreEnd
    }

    /**
     * Returns the chunks of the message to send to the RSysLog server.
     * Note: this specific implementation sends messages as whole JSON Objects,
     * there are no "chunks".
     *
     * @return string A JSON representation of the Log message.
     */
    public function GetMessageChunks()
    {
        return (array($this->FormatMessage()));
    }
}
