<?php

class Mage_Adminhtml_Model_System_Config_Source_Language
{
    public function toOptionArray()
    {
        return Mage::getResourceModel('core/language_collection')->loadData()->toOptionArray();
    }
}