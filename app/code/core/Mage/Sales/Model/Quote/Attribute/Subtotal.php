<?php

class Mage_Sales_Model_Quote_Attribute_Subtotal extends Mage_Sales_Model_Quote_Attribute
{
    function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();

        $items = $quote->getItemsAsArray();
        $subtotal = 0;
        $weight = 0;
        foreach ($items as $item) {
            $subtotal += $item['row_total'];
            $weight += $item['weight'];
        }

        $arr[] = array('code'=>'subtotal', 'title'=>'Subtotal:', 'value'=>$subtotal, 'output'=>true);
        $arr[] = array('code'=>'weight', 'value'=>$subtotal);
        return $arr;
    }
}