<?php
/**
 * @author Colin Mollenhour
 */
class Hackathon_Logger_Formatter_Advanced extends Zend_Log_Formatter_Simple
{

  const DEFAULT_FORMAT = '%timestamp% %priorityName% (%priority%): %message%';

  public function __construct($format = NULL)
  {
    $configFormat = Mage::getStoreConfig('logger/general/format');
    if ($configFormat) {
      $format = str_replace('\n', PHP_EOL, $configFormat);
    }
    if ( ! $format) {
      $format = self::DEFAULT_FORMAT . PHP_EOL;
    }
    parent::__construct($format);
  }

  /**
   * Formats data into a single line to be written by the writer.
   *
   * @param array $event
   * @return string             formatted line to write to the log
   */
  public function format($event)
  {
    Mage::helper('hackathon_logger')->addEventMetadata($event, '-');
    return parent::format($event);
  }

}
