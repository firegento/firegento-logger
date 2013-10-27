<?php
/**
Copyright (c) 2013 Diego Zanella (http://dev.pathtoenlightenment.net)

@package Remote SysLog
@author Diego Zanella <diego@pathtoenlightenment.net>
@copyright Copyright (c) 2013 Diego Zanella (http://dev.pathtoenlightenment.net)
@license http://dev.pathtoenlightenment.net/noncommercial-licence/ Noncommercial Licence
*/

/**
 * Holds the possible values for Syslog Severity
 */
final class SyslogSeverity {
	const EMERGENCY = 0;
	const ALERT = 1;
	const CRITICAL = 2;
	const ERROR = 3;
	const WARNING = 4;
	const NOTICE = 5;
	const INFO = 6;
	const DEBUG = 7;

	/**
	 * Checks if a value is a valid Syslog Severity.
	 *
	 * @param Severity The value to validate.
	 * @return True if the value is a valid Severity, False otherwise.
	 */
	public static function IsValidSeverity($Severity) {
		return isset($Severity) &&
					 $Severity >= self::EMERGENCY &&
					 $Severity <= self::DEBUG;
	}
}
