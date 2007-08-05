<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Shipping
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend
{
    public function collectTotals(Mage_Sales_Model_Quote_Address $address)
    {
        /*
        $addressEntities = $quote->getEntitiesByType('address');
        $method = $quote->getShippingMethod();
        if ($method) {
            $amount = $quote->getShippingAmount();
            if (!$amount) {
                
                $quote->setShippingAmount($amount);
            }
        }
        */ 
        $oldWeight = $address->getWeight();
        
        $address->setWeight(0);

        foreach ($address->getAllItems() as $item) {
            $item->calcRowWeight();
            $address->setWeight($address->getWeight() + $item->getRowWeight());
        }
        
        if ($address->getEstimatePostcode() && $oldWeight!=$address->getWeight()) {
            $address->estimateShippingMethods();
        }
        
        $address->setGrandTotal($address->getGrandTotal() + $address->getShippingAmount());
        return $this;
    }


}