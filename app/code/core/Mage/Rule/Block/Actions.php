<?php

class Mage_Rule_Block_Actions implements Varien_Data_Form_Element_Renderer_Interface
{
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
	    if ($element->getRule() && $element->getRule()->getActions()) {
	       return $element->getRule()->getActions()->asHtmlRecursive();
	    } 
	    return '';
	}
}