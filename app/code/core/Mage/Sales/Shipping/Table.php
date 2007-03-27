<?php

class Mage_Sales_Shipping_Table extends Mage_Sales_Shipping_Abstract
{
    public function fetchQuotes($data, $refresh=false) 
    {
        $arr = array(
            'table'=>array(
                'free'=>array('title'=>'Free shipping', 'price'=>0),
             ),
        );
        
        return $arr;
    }
}