<?php

class Mage_Sales_Model_Quote_Attribute_Subtotal extends Mage_Sales_Model_Quote_Attribute
{
    function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();
        
        $subtotal = 0;
        $weight = 0;

        foreach ($quote->getEntitiesByType('item') as $item) {
            $item->setRowTotal($item->getPrice()*$item->getQty());
            $item->setRowWeight($item->getWeight()*$item->getQty());
            $subtotal += $item->getRowTotal();
            $weight += $item->getRowWeight();
        }
        $quote->setSubtotal($subtotal)->setWeight($weight);
            
        return $this;
    }
    
    function getTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr['subtotal'] = array('code'=>'subtotal', 'title'=>__('Subtotal'), 'value'=>$quote->getSubtotal(), 'output'=>true);

        return $arr;
    }
}