<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Shipping
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend
{
    public function collectTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $oldWeight = $address->getWeight();
        
        $address->setWeight(0);

        foreach ($address->getAllItems() as $item) {
            $item->calcRowWeight();
            $address->setWeight($address->getWeight() + $item->getRowWeight());
        }
        
        if ($address->getPostcode() && $oldWeight!=$address->getWeight()) {
            $address->collectShippingRates();
        }
        
        $address->setShippingAmount(0);
        $method = $address->getShippingMethod();
        if ($method) {
            foreach ($address->getAllShippingRates() as $rate) {
                if ($rate->getCode()==$method) {
                    $address->setShippingAmount($rate->getPrice());
                    $address->setShippingDescription($rate->getCarrierTitle().' - '.$rate->getMethodDescription());
                    break;
                }
            }
        }
        
        $address->setGrandTotal($address->getGrandTotal() + $address->getShippingAmount());
        return $this;
    }


}