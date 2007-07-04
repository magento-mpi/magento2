<?php
/**
 * Radio buttons collection
 *
 * @package     Varien
 * @subpackage  Data
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Varien_Data_Form_Element_Radios extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('radios');
    }
    
    public function getSeparator()
    {
        $separator = $this->getData('separator');
        if (is_null($separator)) {
            $separator = '&nbsp;';
        }
        return $separator;
    }
    
    public function toHtml()
    {
        $html = '<span class="field-row">'."\n";
        if ($this->getLabel()) {
            $html.= '<label for="'.$this->getHtmlId().'">'.$this->getLabel().'</label>'."\n";
        }
        
        $value = $this->getValue();
        if ($values = $this->getValues()) {
            foreach ($values as $option) {
                $html.= $this->_optionToHtml($option, $value);
            }
        }
        $html.= '</span>'."\n";
        return $html;
    }
    
    protected function _optionToHtml($option, $selected)
    {
        $html = '<input type="radio"'.$this->serialize(array('name', 'class', 'style'));
        if (is_array($option)) {
            $html.= 'value="'.$option['value'].'"  id="'.$this->getHtmlId().$option['value'].'"';
            if ($option['value'] == $selected) {
                $html.= ' checked="true"';
            }
            $html.= '/>';
            $html.= '<label class="inline" for="'.$this->getHtmlId().$option['value'].'">'.$option['label'].'</label>';
        }
        elseif ($option instanceof Varien_Object) {
        	$html.= 'id="'.$this->getHtmlId().$option->getValue().'"'.$option->serialize(array('label', 'title', 'value', 'class', 'style'));
        	if (in_array($option->getValue(), $selected)) {
        	    $html.= ' checked="true"';
        	}
        	$html.= '>';
        	$html.= '<label class="inline" for="'.$this->getHtmlId().$option->getValue().'">'.$option->getLabel().'</label>';
        }
        $html.= $this->getSeparator() . "\n";
        return $html;
    }
}
