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
        $id = $element->getHtmlId();
        $default = !$this->getRequest()->getParam('website') && !$this->getRequest()->getParam('store');

        $html = '<h4>'.$element->getLegend().'</h4>';
        $html.= '<fieldset class="config" id="'.$element->getHtmlId().'">';
        $html.= '<legend>'.$element->getLegend().'</legend>';
        
        // field label column
        $html.= '<table cellspacing=0>';
        $html.= '<col class="label"/><col class="custom"/>';
        if (!$default) {
            $html .= '<col class="inherit"/>';
        }
        $html.= '<col class="old"/>';
        $html.= '<thead><tr><th>&nbsp;</th><th>';
        
        // custom column
        $html.= '<input id="'.$id.'_custom" name="groups['.$id.'][inherit]" type="radio" value="0" class="input-radio">'; 
        $html.= '<label for="'.$id.'_custom">'.__('Specific').'</label>';
        
        if (!$default) {
            $html.= '</th><th>';

            // default column
            $html.= '<input id="'.$id.'_inherit" name="groups['.$id.'][inherit]" type="radio" value="1" class="input-radio">'; 
            $html.= '<label for="'.$id.'_inherit">'.__('Default').'</label>';
        }

        $html.= '</th><th>';
        
        // old column
        $html.= '<input id="'.$id.'_old" name="groups['.$id.'][inherit]" type="radio" value="-1" class="input-radio">'; 
        $html.= '<label for="'.$id.'_old">'.__('Previous').'</label>';
        
        $html.= '</th></tr></thead><tbody>';
        
        foreach ($element->getElements() as $field) {
        	$html.= $field->toHtml();
        }
        
        $html.= '</tbody></table></fieldset>';
        return $html;
    }
}
