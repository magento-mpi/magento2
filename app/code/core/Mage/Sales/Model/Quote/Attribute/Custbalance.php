<?php

class Mage_Sales_Model_Quote_Attribute_Custbalance extends Mage_Sales_Model_Quote_Attribute
{
    public function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        $quote->setCustbalanceAmount(10);
        
        $quote->setGrandTotal($quote->getGrandTotal()-$quote->getCustbalanceAmount());
        
        return $this;
    }
    
    public function getTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();
        
        $custbalance = $quote->getCustbalanceAmount();
        if ($custbalance) {
            $arr['custbalance'] = array('code'=>'custbalance', 'title'=>__('Store credit'), 'value'=>-$custbalance, 'output'=>true);
        }

        return $arr;
    }
}