<?php


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
DROP TABLE if exists {$this->getTable('advanced_logger')};
CREATE TABLE {$this->getTable('advanced_logger')} (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `message` text,
  `severity` int(2),
  `timestamp` timestamp default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`entity_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
");

$installer->endSetup();
