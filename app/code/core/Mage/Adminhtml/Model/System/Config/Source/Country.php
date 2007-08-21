<?php

class Mage_Adminhtml_Model_System_Config_Source_Country
{
    public function toOptionArray($isMultiselect=false)
    {
        if($isMultiselect){
            $firstItem = false;
        }
        else{
            $firstItem = '';
        }
        return Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray($firstItem);
    }
}