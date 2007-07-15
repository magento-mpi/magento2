<?php

class Mage_Adminhtml_Block_System_Config_Form_Fieldset_End extends Varien_Data_Form_Element_Abstract 
{
    public function toHtml()
    {
        $html = '</tbody></table>';
        return $html;
    }
}