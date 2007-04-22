<?php

class Mage_Sales_Model_Quote_Attribute_Shipping extends Mage_Sales_Model_Quote_Attribute
{
    function collectTotals(Mage_Sales_Model_Quote $quote)
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
        
        $quote->setGrandTotal($quote->getGrandTotal()+$quote->getShippingAmount());
        return $this;
    }
    
    function getTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();
        $amount = $quote->getShippingAmount();
        if ($amount) {
            $arr['shipping'] = array('code'=>'shipping', 'title'=>__('Shipping & Handling').' ('.$quote->getShippingDescription().')', 'value'=>$quote->getShippingAmount(), 'output'=>true);
        }
        return $arr;
    }

}