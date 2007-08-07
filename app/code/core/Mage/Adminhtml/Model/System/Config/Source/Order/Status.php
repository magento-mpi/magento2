<?php

class Mage_Adminhtml_Model_System_Config_Source_Order_Status
{

    public function toOptionArray()
    {
        return Mage::getResourceModel('sales/order_status_collection')
            ->load()
            ->toOptionArray();
    }

}
