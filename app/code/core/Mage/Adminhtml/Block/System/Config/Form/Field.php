<?php
/**
 * Abstract config form element renderer
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_System_Config_Form_Field
    extends Mage_Core_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $id = $element->getHtmlId();
        
        // replace [value] with [inherit]
        $radioName = substr($element->getName(), 0, strlen($element->getName())-7).'[inherit]';
        
        $inherit = $element->getInherit() ? 'checked' : '';
        $custom = !$element->getInherit() ? 'checked' : '';
        
        $html = '<tr><td class="label">'.$element->getLabel().'</td><td>';
        
        // default value
        $html.= '<input id="'.$id.'_inherit" name="'.$radioName.'" type="radio" value="1" class="input-radio" '.$inherit.'>';
        $html.= $element->getDefaultValue();
        
        $html.= '</td><td>';
        
        // custom value
        $html.= '<input id="'.$id.'_custom" name="'.$radioName.'" type="radio" value="0" class="input-radio" '.$custom.'>';
        $html.= $element->getElementHtml();
        
        $html.= '</td></tr>';
        return $html;
    }
}
