<?php

/**
 * Observer Model for the Hackathon Logger Module.
 *
 * This class contains methods executed by magento crons and events configured
 * in the config.xml of this module.
 *
 * @copyright SYNAXON AG
 * @package   Hackathon_Logger
 * @author    Daniel Kröger <daniel.kroeger@synaxon.de>
 * @version   31.05.2013
 */

class Hackathon_Logger_Model_Observer
{
    const DB_TIMESTAMP_FORMAT = 'Y-m-d H:i:s';

    /**
     * Called by cron expression in config.xml to cleanup the
     * logs written to the DB.
     */
    public function clean_logs()
    {
        // save default app timezone ...
        $timezone  = date_default_timezone_get();
        date_default_timezone_set('Europe/Berlin');

        $counter   = 0;
        $delete    = (time() - (60*60*24*7));
        $messages  = Mage::getModel('hackathon_logger/db_entry')->getCollection();

        foreach($messages as $message)
        {
            $db = strtotime($message->getTimestamp());

            if ($delete > $db)
            {
                $counter++;
                $message->delete();
            }
        }

        Mage::log('[CRONJOB: clean_logs] Deleted ' . $counter .' log message(s) from DB that are older than 45 days.', Zend_Log::INFO);

        // reset timezone ...
        date_default_timezone_set($timezone);
    }
}
