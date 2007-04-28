<?php

class Mage_Sales_Model_Quote_Attribute_Discount extends Mage_Sales_Model_Quote_Attribute
{
    public function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        $coupon = Mage::getModel('sales', 'discount_coupon')->loadByCode($quote->getCouponCode());
        #print_r($coupon); die;
        $quote->setDiscountAmount(0);
        
        if (!empty($coupon) && $coupon->isValid()) {
            $coupon->setQuoteDiscount($quote);
        } else {
            $quote->setCouponCode('');
            $quote->setDiscountPercent(0);
        }
            
        $quote->setGrandTotal($quote->getGrandTotal()-$quote->getDiscountAmount());

        return $this;
    }
    
    public function getTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();
        
        $amount = $quote->getDiscountAmount();
        if ($amount) {
            $arr['discount'] = array('code'=>'discount', 'title'=>__('Discount').' ('.$quote->getCouponCode().')', 'value'=>-$amount, 'output'=>true);
        }

        return $arr;
    }

}