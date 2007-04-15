<?php

class Mage_Sales_Model_Quote_Attribute_Giftcert extends Mage_Sales_Model_Quote_Attribute
{
    public function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();
        
        $code = $quote->getGiftcertCode();
        $gift = Mage::getModel('sales_resource', 'giftcert')->getGiftcertByCode($code);
        if ($gift) {
            $giftAmount = min($quote->getGrandTotal(), $gift['balance_amount']);
        } else {
            $giftAmount = 0;
        }

        $quote->setGiftcertAmount($giftAmount);
        
        return $this;
    }
    
    public function getTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();
        
        $amount = $quote->getGiftcertAmount();
        if ($amount) {
            $arr['giftcert'] = array('code'=>'giftcert', 'title'=>__('Gift certificate').' ('.$quote->getGiftcertCode().')', 'value'=>-$amount, 'output'=>true);
        }

        return $arr;
    }
}