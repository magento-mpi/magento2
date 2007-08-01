<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Custbalance
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend
{
    public function collectTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $address->setCustbalanceAmount(0);
        
        $address->setGrandTotal($address->getGrandTotal() - $address->getCustbalanceAmount());
        
        return $this;
    }
}
