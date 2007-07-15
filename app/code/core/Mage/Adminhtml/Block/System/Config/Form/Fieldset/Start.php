<?php

class Mage_Adminhtml_Block_System_Config_Form_Fieldset_Start extends Varien_Data_Form_Element_Abstract 
{
    public function toHtml()
    {
        $cId = $this->getContainer()->getHtmlId();
        $idPrefix = $cId.'_'.$this->getHtmlId();
        $html = '<table><thead><tr>';
        $html .= '<th>Setting</th>'; // field label column
        $html .= '<th><input id="'.$idPrefix.'_inherit" name="'.$cId.'" type="radio">'; // default column
        $html .= '<label for="'.$idPrefix.'_inherit">'.__('Default').'</label></th>';
        $html .= '<th><input id="'.$idPrefix.'_custom" name="'.$cId.'" type="radio">'; // custom column
        $html .= '<label for="'.$idPrefix.'_custom">'.__('Custom').'</label></th>';
        $html .= '</tr></thead><tbody>';
        return $html;
    }
}