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
    
    public function toHtml()
    {      
    	$html = null;

        if ($this->_renderer) {
            $html = $this->_renderer->render($this);
        }
        else
        {
        	$html.= ( $this->getNoSpan() === true ) ? '' : '<span class="field-row">'."\n";
            if ($this->getLabel()) {
                $html.= '<label for="'.$this->getHtmlId().'">'.$this->getLabel().'</label>'."\n";
            }

            $html.= '<input type="text" name="'.$this->getName().'" id="'.$this->getHtmlId().'" value="'.$this->getEscapedValue().'" class="input-text"/> <img src="' . Mage::getBaseUrl() . $this->getImage() . '" alt="" align="absmiddle" id="'.$this->getHtmlId().'_trig" title="' . __('Select Date') . '" />';
	        $html.= '<script type="text/javascript">
	            Calendar.setup({
	                inputField : "'.$this->getHtmlId().'",
	                ';
			if($this->getTime()) {
				$html.='showsTime:true,' . "\n";
				$html.='ifFormat : "%Y-%m-%d %H:%M",' . "\n";
			} else {
				$html.='ifFormat : "%Y-%m-%d",' . "\n";
			}
	        $html.='button : "'.$this->getHtmlId().'_trig",
	                align : "Bl",
	                singleClick : true
	            });
	        </script>';
            
            $html.= ( $this->getNoSpan() === true ) ? '' : '</span>'."\n";
             
	    }
        
        return $html;
    }
    
    public function getImage()
    {
    	if($this->hasData('image')) {
    		return $this->getData('image');
    	}
    	
    	return '/skins/adminhtml/images/grid-cal.gif';
    }
}// Class Varien_Data_Form_Element_Date END