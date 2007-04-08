<?php

class Mage_Sales_Quote_Total_Subtotal extends Mage_Sales_Quote_Total_Abstract
{
    function getTotals()
    {
        $arr = array();

        $items = $this->_quote->getItems();
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