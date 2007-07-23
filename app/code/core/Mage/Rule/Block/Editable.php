<?php

class Mage_Rule_Block_Editable extends Mage_Core_Block_Abstract 
    implements Varien_Data_Form_Element_Renderer_Interface
{
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
		$html = '<span class="rule-param" id="'.$element->getParamId().'">';
		$html.= '<a href="javascript:void(0)" class="label">';
		$html.= $element->getValueName();
		$html.= '</a><span class="element">';
		$html.= $element->getElementHtml();
		$html.= '</span></span>';
		return $html;
	}
}