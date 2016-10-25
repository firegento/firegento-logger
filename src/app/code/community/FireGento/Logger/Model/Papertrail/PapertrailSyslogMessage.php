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
 * Implementation of Remote Syslog Message for Papertrail.
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   FireGento Team <team@firegento.com>
 */
class FireGento_Logger_Model_Papertrail_PapertrailSyslogMessage extends SyslogMessage
{
    public function GetMessageChunks()
    {
        $MessageTag = trim($this->GetTag());
        $MessageText = trim($this->FormatMessage());

        return [sprintf('%s:%s', $MessageTag, $MessageText)];
    }

    /**
     * Builds the TAG part of the Syslog Message.
     *
     * @return string The TAG part of the Syslog Message, as specified by RFC3164.
     */
    private function GetTag()
    {
        $Tag = sprintf('%s%s %s ', $this->GetPriority(),
            $this->GetSyslogTimestamp(),
            $this->GetHostName());
        return substr($Tag, 0, self::MAX_TAG_LENGTH);
    }

    /**
     * Calculates and returns the Priority of a Log Message.
     *
     * @return string A value indicating the Priority of the Log Message.
     */
    private function GetPriority()
    {
        return sprintf('<%d>', $this->Facility * 8 + $this->Severity);
    }


    /**
     * Returns a Timestamp string, formatted as required by RFC3164.
     *
     * @return string A Timestamp string, formatted as required by RFC3164.
     */
    private function GetSyslogTimestamp()
    {
        // It's a bit messy to format the timestamp as required by RFC3164, because
        // the Day has to be padded with spaces and date() doesn't allow it. For this
        // reason, the Timestamp is first formatted as "Month %2s Hours:Minutes:Seconds",
        // then this string is used with sprintf to pad the day returned by the second
        // date() statement.
        return sprintf(date('M %2\s H:i:s', $this->Timestamp), date('j', $this->Timestamp));
    }
}