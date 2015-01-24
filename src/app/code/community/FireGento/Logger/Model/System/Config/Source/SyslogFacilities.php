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
 * @author    Lee Saferite <lee.saferite@aoe.com>
 * @copyright Lee Saferite <lee.saferite@aoe.com>
 * @license   http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 */
/**
 * Syslog Facilities
 *
 * @category FireGento
 * @package  FireGento_Logger
 * @author   Lee Saferite <lee.saferite@aoe.com>
 */
class FireGento_Logger_Model_System_Config_Source_SyslogFacilities
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $facilities = array();

        $constants = array(
            'LOG_AUTH',
            'LOG_AUTHPRIV',
            'LOG_CRON',
            'LOG_DAEMON',
            'LOG_KERN',
            'LOG_LOCAL0',
            'LOG_LOCAL1',
            'LOG_LOCAL2',
            'LOG_LOCAL3',
            'LOG_LOCAL4',
            'LOG_LOCAL5',
            'LOG_LOCAL6',
            'LOG_LOCAL7',
            'LOG_LPR',
            'LOG_MAIL',
            'LOG_NEWS',
            'LOG_SYSLOG',
            'LOG_USER',
            'LOG_UUCP'
        );

        foreach ($constants as $constant) {
            if (defined($constant)) {
                $facilities[] = array(
                    'label'  => $constant,
                    'value' => constant($constant)
                );
            }
        }

        return $facilities;
    }
}
