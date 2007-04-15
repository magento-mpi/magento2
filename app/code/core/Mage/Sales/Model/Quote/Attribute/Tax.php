<?php

class Mage_Sales_Model_Quote_Attribute_Tax extends Mage_Sales_Model_Quote_Attribute
{
    function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        $taxPercent = 8.25;
        
        $totalTax = 0;
        foreach ($quote->getEntitiesByType('item') as $item) {
            $item->setTaxPercent($taxPercent);
            $taxAmount = $item->getRowTotal()*$taxPercent/100;
            $item->setTaxAmount($taxAmount);
            $totalTax += $taxAmount;
        }
        $quote->setTaxPercent($taxPercent)->setTaxAmount($totalTax);
        
        return $this;
    }
        
    public function getTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();
        
        $amount = $quote->getTaxAmount();
        if ($amount) {
            $arr['tax'] = array('code'=>'tax', 'title'=>__('Tax').' (CA 8.25%)', 'value'=>$amount, 'output'=>true);
        }

        return $arr;
    }
}