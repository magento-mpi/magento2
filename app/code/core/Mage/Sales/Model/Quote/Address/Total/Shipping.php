<?php

class Mage_Sales_Model_Quote_Address_Total_Shipping
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
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
            	if ($address->getFreeShipping()) {
            		$freeMethod = Mage::getStoreConfig('carriers/'.$rate->getCarrier().'/free_method');
            		if ($rate->getMethod()==$freeMethod) {
            			$rate->setPrice(0);
            		}
            	}
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

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getShippingAmount();
        if ($amount!=0) {
            $address->addTotal(array(
                'code'=>$this->getCode(), 
                'title'=>__('Shipping & Handling').' ('.$address->getShippingDescription().')', 
                'value'=>$address->getShippingAmount()
            ));
        }
        return $this;
    }
}