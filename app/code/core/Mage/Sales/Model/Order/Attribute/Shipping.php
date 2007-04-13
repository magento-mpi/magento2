<?php

class Mage_Sales_Model_Order_Attribute_Shipping extends Mage_Sales_Model_Order_Attribute
{
    function collectTotals(Mage_Sales_Model_Order $order)
    {
        $arr = array();

        #$arr['shipping'] = array('code'=>'shipping', 'title'=>__('Shipping & Handling'), 'value'=>$subtotal, 'output'=>true);
        
        return $arr;
    }
}