<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Subtotal
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend
{
    public function collectTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $address->setSubtotal(0);

        foreach ($address->getAllItems() as $item) {
            $item->setRowTotal($item->getPrice() * $item->getQty());
            $address->setSubtotal($address->getSubtotal() + $item->getRowTotal());
        }
       
        $address->setGrandTotal($address->getSubtotal());
            
        return $this;
    }

}