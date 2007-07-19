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
        $namePrefix = substr($element->getName(), 0, strlen($element->getName())-7);        
        
        $custom = $element->getInherit()==0 ? 'checked' : '';
        $inherit = $element->getInherit()==1 ? 'checked' : '';
        $options = $element->getValues();

        $html = '<tr><td class="label">'.$element->getLabel().'</td><td>';
        
        // custom value
        $html.= '<input id="'.$id.'_custom" name="'.$namePrefix.'[inherit]" type="radio" value="0" class="input-radio" '.$custom.'>';
        $html.= $element->getElementHtml();
        
        if ($this->getRequest()->getParam('website') || $this->getRequest()->getParam('store')) {
            $html.= '</td><td>';
            
            $defText = $element->getDefaultValue();
            if ($options) {
                foreach ($options as $k=>$v) {
                    if ($k==$element->getDefaultValue()) {
                        $defText = $options[$k]['label'];
                        break;
                    }
                }
            }
            
            // default value
            $html.= '<input id="'.$id.'_inherit" name="'.$namePrefix.'[inherit]" type="radio" value="1" class="input-radio" '.$inherit.'>';
            $html.= '<label for="'.$id.'_inherit" class="inherit">'.$defText.'</label>';
            $html.= '<input type="hidden" name="'.$namePrefix.'[default_value]" value="'.$element->getDefaultValue().'">';
        }
            
        $html.= '</td><td>';
        
        $oldText = $element->getOldValue();
        if ($options) {
            foreach ($options as $k=>$v) {
                if ($k==$element->getOldValue()) {
                    $oldText = $options[$k]['label'];
                    break;
                }
            }
        }
        
        // old value
        $html.= '<input id="'.$id.'_old" name="'.$namePrefix.'[inherit]" type="radio" value="-1" class="input-radio">';
        $html.= '<label for="'.$id.'_old" class="old">'.$oldText.'</label>';
        $html.= '<input type="hidden" name="'.$namePrefix.'[old_value]" value="'.$element->getOldValue().'">';
        
        $html.= '</td></tr>';
        return $html;
    }
}
