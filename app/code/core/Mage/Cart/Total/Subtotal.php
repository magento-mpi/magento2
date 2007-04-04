<?php

class Mage_Cart_Total_Subtotal extends Mage_Cart_Total_Abstract
{
    function getTotals()
    {
        $arr = array();
        
        $items = Mage::getResourceModel('cart', 'cart')->getItems();

        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['row_total'];
        }

        $arr[] = array('code'=>'subtotal', 'title'=>'Subtotal:', 'value'=>$subtotal);
        return $arr;
    }
}