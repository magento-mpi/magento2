<?php
/**
 * Form select element
 *
 * @package    Varien
 * @subpackage Form
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Varien_Data_Form_Element_Select extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array()) 
    {
        parent::__construct($attributes);
        $this->setType('select');
        $this->setExtType('combobox');
    }
    
    public function toHtml()
    {
        $this->addClass('select');
        $html = '<span class="field-row">'."\n";
        if ($this->getLabel()) {
            $html.= '<label for="'.$this->getHtmlId().'">'.$this->getLabel().'</label>'."\n";
        }
        $html.= '<select id="'.$this->getHtmlId().'" '.$this->serialize($this->getHtmlAttributes()).'>'."\n";
        
        $value = $this->getValue();
        if (!is_array($value)) {
            $value = array($value);
        }
        
        if ($values = $this->getValues()) {
            foreach ($values as $option) {
                if (is_array($option)) {
                    if (is_array($option['value'])) {
                        $html.='<optgroup label="'.$optionInfo['label'].'">'."\n";
                        foreach ($optionInfo['value'] as $groupItem) {
                            $html.= $this->_optionToHtml($groupItem, $value);
                        }
                        $html.='</optgroup>'."\n";
                    }
                    else {
                        $html.= $this->_optionToHtml($option, $value);
                    }
                }
                elseif ($option instanceof Varien_Object) {
                    if (is_array($option->getValue())) {
                        $html.='<optgroup '.$option->serialize('label', 'title', 'class', 'style').'>'."\n";
                        foreach ($option->getValue() as $groupItem) {
                            $html.= $this->_optionToHtml($groupItem, $value);
                        }
                        $html.='</optgroup>'."\n";
                    }
                    else {
                        $html.= $this->_optionToHtml($option, $value);
                    }
                }
            }
        }
        $html.= '</select>'."\n";
        $html.= '</span>'."\n";
        return $html;
    }
    
    protected function _optionToHtml($option, $selected)
    {
        $html = '<option ';
        if (is_array($option)) {
            $html = 'value="'.$option['value'].'"';
            if (in_array($option['value'], $selected)) {
                $html.= ' selected="selected"';
            }
            $html.= '>';
            $html.= $option['label'];
        }
        elseif ($option instanceof Varien_Object) {
        	$html.= $option->serialize(array('label', 'title', 'value', 'class', 'style'));
        	if (in_array($option->getValue(), $selected)) {
        	    $html.= ' selected="selected"';
        	}
        	$html.= '>';
            $html.= $option->getLabel();
        }
        $html.= '</option>'."\n";
        return $html;
    }
}