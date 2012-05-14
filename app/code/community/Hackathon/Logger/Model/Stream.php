<?php
/**
 * Like ZLWStream but overrides the formatter to use the advanced formatter
 *
 * @author Colin Mollenhour
 */

class Hackathon_Logger_Model_Stream extends Zend_Log_Writer_Stream
{

  protected $_enableBacktrace = FALSE;

  /**
   * @param bool $flag
   */
  public function setEnableBacktrace($flag)
  {
    $this->_enableBacktrace = $flag;
  }

  /**
   * Write a message to the log.
   *
   * @param  array  $event  event data
   * @return void
   */
  protected function _write($event)
  {
      $line = $this->_formatter->format($event, $this->_enableBacktrace);

      if (false === @fwrite($this->_stream, $line)) {
          #require_once 'Zend/Log/Exception.php';
          throw new Zend_Log_Exception("Unable to write to stream");
      }
  }

  /**
   * Satisfy newer Zend Framework
   *
   * @static
   * @param $config
   */
  static public function factory($config) {}

}
