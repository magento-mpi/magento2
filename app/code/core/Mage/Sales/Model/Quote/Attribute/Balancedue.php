<?php

class Mage_Sales_Model_Quote_Attribute_Balancedue extends Mage_Sales_Model_Quote_Attribute
{
    function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        $balanceDue = $quote->getGrandTotal();
        $balanceDue -= $quote->getCustbalanceAmount();
        $balanceDue -= $quote->getGiftcertAmount();
        
        $quote->setBalanceDue($balanceDue);
        
        return $this;
    }
    
    public function getTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();

        $arr['balance_due'] = array('code'=>'balance_due', 'title'=>__('Balance Due'), 'value'=>$quote->getBalanceDue(), 'output'=>true, 'style'=>'font-weight:bold; color:red');

        return $arr;
    }
}