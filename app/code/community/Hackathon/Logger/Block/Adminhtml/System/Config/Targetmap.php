<?php
/**
 * @author Colin Mollenhour
 */
class Hackathon_Logger_Block_Adminhtml_System_Config_Targetmap extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    protected function _prepareToRender()
    {
        $this->addColumn('pattern', array(
            'label' => Mage::helper('hackathon_logger')->__('Pattern'),
            'style' => 'width:200px',
        ));

        $targetRenderer = new Hackathon_Logger_Block_Adminhtml_System_Config_Renderer_Select;
        $targetRenderer->setValues(Mage::getSingleton('hackathon_logger/system_config_source_targets')->toOptionArray());
        $this->addColumn('target', array(
            'label' => Mage::helper('hackathon_logger')->__('Target'),
            'style' => 'width:180px',
            'renderer' => $targetRenderer,
        ));

        $btRenderer = new Hackathon_Logger_Block_Adminhtml_System_Config_Renderer_Select;
        $btRenderer->setValues(Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray());
        $this->addColumn('backtrace', array(
            'label' => Mage::helper('hackathon_logger')->__('Backtrace'),
            'style' => 'width:60px',
            'renderer' => $btRenderer,
        ));

        $somRenderer = new Hackathon_Logger_Block_Adminhtml_System_Config_Renderer_Select;
        $somRenderer->setValues(Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray());
        $this->addColumn('stop_on_match', array(
            'label' => Mage::helper('hackathon_logger')->__('Stop On Match'),
            'style' => 'width:60px',
            'renderer' => $somRenderer,
        ));

        $this->_addButtonLabel = Mage::helper('hackathon_logger')->__('Add Target Rule');
    }

    protected function _toHtml()
    {
        // Make sure id is set before template is rendered or else we can't know the id.
        if ( ! $this->getHtmlId()) {
            $this->setHtmlId('_' . uniqid());
        }
        $html = parent::_toHtml();

        // Scripts in the template must be evaluated so that select values can be set.
        $html .= "
        <script type='text/javascript'>
        arrayRow{$this->getHtmlId()}._add = arrayRow{$this->getHtmlId()}.add;
        arrayRow{$this->getHtmlId()}.add = function(templateData, insertAfterId) {
          this._add(templateData, insertAfterId);
          this.template.evaluate(templateData).evalScripts();
        }
        </script>
        ";
        return $html;
    }
}
