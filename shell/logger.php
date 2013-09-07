<?php
require_once 'log.php';

/**
 * Magento Log Shell Script
 *
 * @category    Mage
 * @package     Mage_Shell
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Firegento_Logger_Shell extends Mage_Shell_Log
{

    public function run()
    {
        if ($this->getArg('clean'))
        {
            $days = $this->getArg('days');
            /** @var $model Firegento_Logger_Model_Observer */
            $model = Mage::getModel('firegento_logger/observer');
            $model->cleanLogs(new Varien_Event_Observer(), $days);

            echo "Database log cleaned\n";
        }
        parent::run();
    }
}

$shell = new Firegento_Logger_Shell();
$shell->run();
