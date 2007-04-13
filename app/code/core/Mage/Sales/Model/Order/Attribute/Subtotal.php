<?php

class Mage_Sales_Model_Order_Attribute_Subtotal extends Mage_Sales_Model_Order_Attribute
{
    function collectTotals(Mage_Sales_Model_Order $order)
    {
        $arr = array();
        
        $subtotal = 0;
        $weight = 0;

        foreach ($order->getEntitiesByType('item') as $item) {
            $item->setAttribute('row_total', $item->getAttribute('price')*$item->getAttribute('qty'));
            $item->setAttribute('row_weight', $item->getAttribute('weight')*$item->getAttribute('qty'));
            $subtotal += $item->getAttribute('row_total');
            $weight += $item->getAttribute('row_weight');
        }
        $order->getOrderEntity()
            ->setAttribute('subtotal', $subtotal)
            ->setAttribute('weight', $weight);

        $arr['subtotal'] = array('code'=>'subtotal', 'title'=>__('Subtotal'), 'value'=>$subtotal, 'output'=>true);
        $arr['weight'] = array('code'=>'weight', 'value'=>$weight);

        return $arr;
    }
}