<?php

class Mage_Sales_Model_Quote_Attribute_Shipping extends Mage_Sales_Model_Quote_Attribute
{
    function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();

        $arr['shipping'] = array('code'=>'shipping', 'title'=>__('Shipping & Handling'), 'value'=>$subtotal, 'output'=>true);
        
        return $arr;
    }
}