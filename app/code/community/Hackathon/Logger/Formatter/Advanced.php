<?php
/**
 * @author Colin Mollenhour
 */
class Hackathon_Logger_Formatter_Advanced extends Zend_Log_Formatter_Simple
{

  const DEFAULT_FORMAT = '%timestamp% %priorityName% (%priority%): %message%';

  /**
   * @param null|string $format
   */
  public function __construct($format = NULL)
  {
    $configFormat = Mage::helper('hackathon_logger')->getLoggerConfig('general/format');
    if ($configFormat) {
      $format = str_replace('\n', PHP_EOL, $configFormat);
    }
    if ( ! $format) {
      $format = self::DEFAULT_FORMAT;
    }
    parent::__construct($format . PHP_EOL);
  }

  /**
   * Formats data into a single line to be written by the writer.
   *
   * @param array $event
   * @param bool $enableBacktrace
   * @return string             formatted line to write to the log
   */
  public function format($event, $enableBacktrace = FALSE)
  {
    Mage::helper('hackathon_logger')->addEventMetadata($event, '-', $enableBacktrace);
    return parent::format($event);
  }

}
