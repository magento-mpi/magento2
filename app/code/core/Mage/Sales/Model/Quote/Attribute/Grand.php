<?php

class Mage_Sales_Model_Quote_Attribute_Grand extends Mage_Sales_Model_Quote_Attribute
{
    function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();

        $grandTotal = $quote->getSubtotal();
        $grandTotal -= $quote->getDiscountAmount();
        $grandTotal += $quote->getShippingAmount();
        $grandTotal += $quote->getTaxAmount();
        
        $quote->setGrandTotal($grandTotal);
        
        return $this;
    }
    
    public function getTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();

        $arr['grand_total'] = array('code'=>'grand_total', 'title'=>__('Grand total'), 'value'=>$quote->getGrandTotal(), 'output'=>true, 'style'=>'font-weight:bold');

        return $arr;
    }
}