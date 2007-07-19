<?php

class Mage_Adminhtml_Model_System_Config_Source_Currency
{
    public function toOptionArray()
    {
        return Mage::getResourceModel('directory/currency_collection')->loadData()->toOptionArray(); 
    }
}