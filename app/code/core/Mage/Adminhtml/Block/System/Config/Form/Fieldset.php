<?php
/**
 * Fieldset config form element renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_System_Config_Form_Fieldset 
    extends Mage_Core_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
		$html = $this->_getHeaderHtml($element);
		
        foreach ($element->getElements() as $field) {
        	$html.= $field->toHtml();
        }
        
        $html .= $this->_getFooterHtml($element);

        return $html;
    }
    
    protected function _getHeaderHtml($element)
    {
        $id = $element->getHtmlId();
        $default = !$this->getRequest()->getParam('website') && !$this->getRequest()->getParam('store');

        $html = '<h4 class="icon-head head-edit-form">'.$element->getLegend().'</h4>';
        $html.= '<fieldset class="config" id="'.$element->getHtmlId().'">';
        $html.= '<legend>'.$element->getLegend().'</legend>';
        
        // field label column
        $html.= '<table cellspacing=0><colgroup class="label"/><colgroup class="value"/>';
        if (!$default) {
            $html.= '<colgroup class="default"/>';
        }
        $html.= '<tbody>';
        
        return $html;
    }
    
    protected function _getFooterHtml($element)
    {
        $html = '</tbody></table></fieldset>';
        return $html;
    }
}
