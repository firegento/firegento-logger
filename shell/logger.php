<?php
require_once 'abstract.php';

class Firegento_Logger_Shell extends Mage_Shell_Abstract
{

    public function run()
    {
        /** @var $model Firegento_Logger_Model_Observer */
        $model = Mage::getModel('firegento_logger/observer');
        if ($this->getArg('clean'))
        {
            $days = $this->getArg('days');
            $model->cleanLogs(new Varien_Event_Observer(), $days);

            echo "Database log cleaned." . PHP_EOL;
        }
        elseif ($this->getArg('rotate'))
        {
            $model->rotateLogs(new Varien_Event_Observer());
            echo "Rotation of log files finished.".PHP_EOL;
        }
        else
        {
            echo $this->usageHelp();
        }
    }

    /**
     * Retrieve Usage Help Message

     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php -f log.php -- [options]
        php -f log.php -- clean --days 1

  clean             Clean Logs
  --days <days>     Save log, days. (Minimum 1 day, if defined - ignoring system value)
  rotate            Rotate every file in var/log with ends with .log
  help              This help

USAGE;
    }
}

$shell = new Firegento_Logger_Shell();
$shell->run();
