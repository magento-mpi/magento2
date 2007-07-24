<?php
/**
 * Varien data selector form element
 *
 * @package    Varien
 * @subpackage Form
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Varien_Data_Form_Element_Date extends Varien_Data_Form_Element_Abstract 
{
	public function __construct($attributes=array()) 
    {
        parent::__construct($attributes);
        $this->setType('input');
        $this->setExtType('textfield');
    }
    
    public function getElementHtml()
    {      
    	$html = null;

    	if (!($datetimeFormat = $this->getFormat())){
            if($this->getTime()) {
        		$datetimeFormat = '%m/%d/%y %I:%M %p';
            } else {
            	$datetimeFormat = '%m/%d/%y'; 
            }
            $this->setFormat($datetimeFormat);
            
        } 
        
        $this->addClass('input-text');
        
        $html = '<input type="text" name="'.$this->getName().'" id="'.$this->getHtmlId().'" value="'.$this->getEscapedValue().'" ' . $this->serialize($this->getHtmlAttributes()) . '/> ' .( !$this->getDisabled() ? '<img src="' . $this->getImage() . '" alt="" align="absmiddle" id="'.$this->getHtmlId().'_trig" title="' . __('Select Date') . '" />' : '');
        $html.= '<script type="text/javascript">
            Calendar.setup({
                inputField : "'.$this->getHtmlId().'",
                ';
              
        
		if($this->getTime()) {
			$html.='showsTime:true,' . "\n";
			$html.='ifFormat : "' . $datetimeFormat . '",' . "\n";
		} else {
			$html.='ifFormat : "' . $datetimeFormat . '",' . "\n";
		}
        $html.='button : "'.$this->getHtmlId().'_trig",
                align : "Bl",
                singleClick : true
            });
        </script>';
        
        return $html;
    }
    
    public function getEscapedValue() {
    	
    	if($this->getFormat() && $this->getValue()) {
    		return strftime($this->getFormat(), strtotime($this->getValue()));
    	}
    	
    	return htmlspecialchars($this->getValue());
    }
    
}// Class Varien_Data_Form_Element_Date END
