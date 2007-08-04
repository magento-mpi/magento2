<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend
    extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract 
{
    public function collectTotals(Mage_Sales_Model_Quote_Address $address)
    {
        return $this;
    }
}