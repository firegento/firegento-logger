<?php
/**
 * @author Colin Mollenhour
 */
class Hackathon_Logger_Block_Adminhtml_System_Config_Renderer_Select extends Mage_Core_Block_Abstract
{

  /**
   * @return string
   */
  protected function _toHtml()
  {
    $htmlId = $this->getColumnName().'#{_id}';
    $select = new Varien_Data_Form_Element_Select(array(
      'html_id' => $htmlId,
      'no_span' => TRUE,
      'name' => $this->getInputName(),
    ));
    $select->addData($this->getColumn());
    $select->setForm(new Varien_Object());
    $select->setValues($this->getValues());

    // Escape properly and use javascript to set the selected values
    return str_replace(array("\n",'"','/'), array('','\"','\/'), "
    {$select->getElementHtml()}
    <script type=\"text\/javascript\">
      $(\"$htmlId\").setValue(\"#{{$this->getColumnName()}}\");
    </script>
    ");
  }

}
