<?php

class Mage_Sales_Model_Quote_Attribute_Tax extends Mage_Sales_Model_Quote_Attribute
{
    function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        $quote->setTaxPercent(8.25);
        $quote->setTaxAmount(0);
        
        foreach ($quote->getEntitiesByType('item') as $item) {
            $item->setTaxPercent($quote->getTaxPercent());
            $item->setTaxAmount($item->getRowTotal()*$item->getTaxPercent()/100);
            $quote->setTaxAmount($quote->getTaxAmount()+$item->getTaxAmount());
        }
        
        $quote->setGrandTotal($quote->getGrandTotal()+$quote->getTaxAmount());
        return $this;
    }
        
    public function getTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();
        
        $amount = $quote->getTaxAmount();
        if ($amount) {
            $arr['tax'] = array('code'=>'tax', 'title'=>__('Tax').' (CA '.number_format($quote->getTaxPercent(),3).'%)', 'value'=>$amount, 'output'=>true);
        }

        return $arr;
    }
}