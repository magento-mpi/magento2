<?php

class Mage_Sales_Model_Quote_Attribute_Subtotal extends Mage_Sales_Model_Quote_Attribute
{
    function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();
        
        $subtotal = 0;
        $weight = 0;

        foreach ($quote->getEntitiesByType('item') as $item) {
            $item->setAttribute('row_total', $item->getAttribute('price')*$item->getAttribute('qty'))->save();
            $subtotal += $item->getAttribute('row_total');
            $weight += $item->getAttribute('weight');
        }

        $arr[] = array('code'=>'subtotal', 'title'=>'Subtotal:', 'value'=>$subtotal, 'output'=>true);
        $arr[] = array('code'=>'weight', 'value'=>$subtotal);
        return $arr;
    }
}