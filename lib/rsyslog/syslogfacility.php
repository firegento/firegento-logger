<?php
/**
Copyright (c) 2013 Diego Zanella (http://dev.pathtoenlightenment.net)

@package Remote SysLog
@author Diego Zanella <diego@pathtoenlightenment.net>
@copyright Copyright (c) 2013 Diego Zanella (http://dev.pathtoenlightenment.net)
@license http://dev.pathtoenlightenment.net/noncommercial-licence/ Noncommercial Licence
*/

/**
 * Holds the possible values for Syslog Facility
 */
final class SyslogFacility {
	const KERNEL = 0;
	const USER = 1;
	const MAIL = 2;
	const SYSDAEMON = 3;
	const SECURITY1 = 4; // Different Operating Systems pass different values for Security, hence the duplicate
	const SYSLOG = 5;
	const LINEPRINTER = 6;
	const NETWORK = 7;
	const UUCP = 8;
	const CLOCK = 9;
	const SECURITY2 = 10; // Different Operating Systems pass different values for Security, hence the duplicate
	const FTPDAEMON = 11;
	const NTP = 12;
	const LOGAUDIT = 13;
	const LOGALERT = 14;
	const CLOCKDAEMON = 15;
	const LOCAL0 = 16;
	const LOCAL1 = 17;
	const LOCAL2 = 18;
	const LOCAL3 = 19;
	const LOCAL4 = 20;
	const LOCAL5 = 21;
	const LOCAL6 = 22;
	const LOCAL7 = 23;

	/**
	 * Checks if a value is a valid Syslog Facility.
	 *
	 * @param Facility The value to validate.
	 * @return True if the value is a valid Facility, False otherwise.
	 */
	public static function IsValidFacility($Facility) {
		return isset($Facility) &&
					 $Facility >= self::KERNEL &&
					 $Facility <= self::LOCAL7;
	}
}
