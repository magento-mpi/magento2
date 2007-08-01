<?php

class Mage_Sales_Model_Quote_Attribute_Grand extends Mage_Sales_Model_Quote_Attribute
{
    public function getTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();

        $arr['grand_total'] = array('code'=>'grand_total', 'title'=>__('Grand Total'), 'value'=>$quote->getGrandTotal(), 'output'=>true, 'style'=>'font-weight:bold');

        return $arr;
    }
}