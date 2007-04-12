<?php

class Mage_Sales_Model_Quote_Attribute_Subtotal extends Mage_Sales_Model_Quote_Attribute
{
    function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();
        
        $subtotal = 0;
        $weight = 0;

        foreach ($quote->getEntitiesByType('item') as $item) {
            $item->setAttribute('row_total', $item->getAttribute('price')*$item->getAttribute('qty'));
            $item->setAttribute('row_weight', $item->getAttribute('weight')*$item->getAttribute('qty'));
            $subtotal += $item->getAttribute('row_total');
            $weight += $item->getAttribute('row_weight');
        }
        $quote->getQuoteEntity()
            ->setAttribute('subtotal', $subtotal)
            ->setAttribute('weight', $weight);

        $arr['subtotal'] = array('code'=>'subtotal', 'title'=>__('Subtotal'), 'value'=>$subtotal, 'output'=>true);
        $arr['weight'] = array('code'=>'weight', 'value'=>$weight);

        return $arr;
    }
}