<?php

class Mage_Sales_Model_Quote_Attribute_Shipping extends Mage_Sales_Model_Quote_Attribute
{
    function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        $addressEntities = $quote->getEntitiesByType('address');
        $method = $quote->getShippingMethod();
        if ($method) {
            $price = $quote->getShippingPrice();
            if (!$price) {
                
                $quote->setShippingPrice($price);
            }
        }
        return $this;
    }
    
    function getTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();
        $method = $quote->getShippingMethod();
        if ($method) {
            $arr['shipping'] = array('code'=>'shipping', 'title'=>__('Shipping & Handling').' ('.$quote->getShippingDescription().')', 'value'=>$quote->getShippingPrice(), 'output'=>true);
        }
        return $arr;
    }

}