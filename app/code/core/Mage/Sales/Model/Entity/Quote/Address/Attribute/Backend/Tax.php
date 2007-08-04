<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Tax
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend
{
    public function collectTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $address->setTaxPercent(8.25);
        $address->setTaxAmount(0);
        
        foreach ($address->getAllItems() as $item) {
            $item->setTaxPercent($address->getTaxPercent());
            $item->setTaxAmount($item->getRowTotal() * $item->getTaxPercent()/100);
            $address->setTaxAmount($address->getTaxAmount() + $item->getTaxAmount());
        }
        
        $address->setGrandTotal($address->getGrandTotal() + $address->getTaxAmount());
        return $this;
    }

}