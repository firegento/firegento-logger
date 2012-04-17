<?php
/**
 * Get list of writer models from config
 *
 * @author Colin Mollenhour
 */
class Hackathon_Logger_Model_System_Config_Source_Writers
{
  protected $_options = array();

  /**
   * @return array
   */
  public function toOptionArray()
  {
    if ( ! $this->_options)
    {
      foreach(Mage::app()->getConfig()->getNode('global/writer_models')->children() as $writer) {
        $module = isset($writer->label['module']) ? $writer->label['module'] : 'hackathon_logger';
        $label = Mage::helper($module)->__($writer->label);
        $this->_options[] = array('label' => $label, 'value' => $writer->class);
      }
    }
    return $this->_options;
  }

}
