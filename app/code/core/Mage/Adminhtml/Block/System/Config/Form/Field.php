<?php

class Mage_Adminhtml_Block_System_Config_Form_Field extends Varien_Data_Form_Element_Abstract 
{
    public function toHtml()
    {
        $html = '<tr><td>label</td><td>Default</td><td>Custom</td></tr>';
        return $html;
    }
}