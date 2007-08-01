<?php

class Mage_Sales_Model_Quote_Attribute_Giftcert extends Mage_Sales_Model_Quote_Attribute
{
    public function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        $gift = Mage::getResourceModel('sales/giftcert')->getGiftcertByCode($quote->getGiftcertCode());
        if ($gift) {
            $quote->setGiftcertAmount(min($quote->getGrandTotal(), $gift['balance_amount']));
        } else {
            $quote->setGiftcertAmount(0);
        }
        
        $quote->setGrandTotal($quote->getGrandTotal()-$quote->getGiftcertAmount());
        
        return $this;
    }
    
    public function getTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();
        
        $amount = $quote->getGiftcertAmount();
        if ($amount) {
            $arr['giftcert'] = array('code'=>'giftcert', 'title'=>__('Gift Certificate').' ('.$quote->getGiftcertCode().')', 'value'=>-$amount, 'output'=>true);
        }

        return $arr;
    }
}
