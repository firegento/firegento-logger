<?php
/**
 * Like ZLWStream but overrides the formatter to use the advanced formatter
 *
 * @author Colin Mollenhour
 */

class Hackathon_Logger_Model_Stream extends Zend_Log_Writer_Stream
{

  /**
   * Overrode this method since Mage::log doesn't let us set a formatter any other way.
   *
   * @param  Zend_Log_Formatter_Interface $formatter
   */
  public function setFormatter($formatter)
  {
    $this->_formatter = new Hackathon_Logger_Formatter_Advanced;
  }

}
