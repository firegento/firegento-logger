<?php
/**
 * User: damian
 * Date: 31.03.12
 * Time: 15:36
 */
class Hackathon_Logger_Model_System_Config_Source_Targets
{
  protected $_options = array();

  public function toOptionArray()
  {
    if ( ! $this->_options)
    {
      foreach(Mage::app()->getConfig()->getNode('global/log/core/writer_models')->children() as $writer) {
        $module = isset($writer->label['module']) ? $writer->label['module'] : 'hackathon_logger';
        $label = Mage::helper($module)->__((string)$writer->label);
        $this->_options[] = array('label' => $label, 'value' => $writer->getName());
      }
    }
    return $this->_options;
  }

}
