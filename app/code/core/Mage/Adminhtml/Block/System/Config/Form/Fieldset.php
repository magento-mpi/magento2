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

        $html = '<h4>'.$element->getLegend().'</h4>';
        $html.= '<fieldset class="config" id="'.$element->getHtmlId().'">';
        $html.= '<legend>'.$element->getLegend().'</legend>';
        
        // field label column
        $html.= '<table cellspacing=0><thead><tr><th class="label">&nbsp;</th><th>'; 
        
        // default column
        $html.= '<input id="'.$id.'_inherit" name="'.$id.'[inherit]" type="radio" value="1" class="input-radio">'; 
        $html.= '<label for="'.$id.'_inherit">'.__('Default').'</label>';
        $html.= '</th><th>';
        
        // custom column
        $html.= '<input id="'.$id.'_custom" name="'.$id.'[inherit]" type="radio" value="0" class="input-radio">'; 
        $html.= '<label for="'.$id.'_custom">'.__('Specific').'</label>';
        
        $html.= '</th></tr></thead><tbody>';
        
        foreach ($element->getElements() as $field) {
        	$html.= $field->toHtml();
        }
        
        $html.= '</tbody></table></fieldset>';
        return $html;
    }
}
