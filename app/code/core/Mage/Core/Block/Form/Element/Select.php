<?php



/**
 * Form select block
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Block_Form_Element_Select extends Mage_Core_Block_Form_Element_Abstract 
{
    public public function __construct($attributes) 
    {
        parent::__construct($attributes);
    }
    
    public function toString()
    {
        $html = $this->renderElementLabel();
        $html.= '<select ';
        $html.= $this->_attributesToString(array(
                'name'
               ,'id'
               ,'title'
               ,'size'
               ,'accesskey'
               ,'tabindex'
               ,'class'
               ,'style'
               ,'disabled'
               ,'onclick'
               ,'onchange'
               ,'onselect'
               ,'onfocus'
               ,'onblur'
               ,'ext_type'));
        $html.= '>';
        
        $value  = $this->getAttribute('value');
        $values = $this->getAttribute('values');
        
        if (!is_array($value)) {
            $value = array($value);
        }
        
        if (is_array($values)) {
            foreach ($values as $optionInfo) {
                if (is_array($optionInfo['value'])) {
                    $html.='<optgroup label="'.$optionInfo['label'].'">';
                    foreach ($optionInfo['value'] as $groupItem) {
                        $html.= $this->_renderOption($groupItem, $value);
                    }
                    $html.='</optgroup>';
                }
                else {
                    $html.= $this->_renderOption($optionInfo, $value);
                }
            }
        }
        
        $html.= '</select>';
        
        return $html;
    }
    
    protected function _renderOption($optionInfo, $selected)
    {
        $html = '<option value="'.htmlspecialchars($optionInfo['value'], ENT_COMPAT).'"';
        if (in_array($optionInfo['value'], $selected)) {
            $html.= ' selected="selected"';
        }
        $html.= '>';
        $html.= htmlspecialchars($optionInfo['label'], ENT_COMPAT);
        $html.= '</option>';
        return $html;
    }
}