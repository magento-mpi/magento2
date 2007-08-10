<?php

class Mage_Rule_Block_Editable extends Mage_Core_Block_Abstract
    implements Varien_Data_Form_Element_Renderer_Interface
{
	public function render(Varien_Data_Form_Element_Abstract $element)
	{
	    $valueName = $element->getValueName();
	    if ($valueName=='') {
	        $valueName = '...';
	    } elseif (strlen($valueName)>30) {
	        $valueName = substr($valueName, 0, 30).'...';
	    }
		$html = '&nbsp;<span class="rule-param" id="'.$element->getParamId().'">';
		$html.= '<a href="javascript:void(0)" class="label">';
		$html.= $valueName;
		$html.= '</a><span class="element">';
		$html.= $element->getElementHtml();
		$html.= '</span></span>&nbsp;';
		return $html;
	}
}