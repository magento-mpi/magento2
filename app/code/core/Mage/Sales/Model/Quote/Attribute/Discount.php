<?php

class Mage_Sales_Model_Quote_Attribute_Discount extends Mage_Sales_Model_Quote_Attribute
{
    public function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        $arr = array();
        
        $couponCode = $quote->getCouponCode();
        $coupon = Mage::getModel('sales_resource', 'discount')->getCouponByCode($couponCode);
        if ($coupon) {
            $discountPercent = $coupon['discount_percent'];
        } else {
            $discountPercent = 0;
        }

        $totalDiscount = 0;
        foreach ($quote->getEntitiesByType('item') as $item) {
            $item->setDiscountPercent($discountPercent);
            $discountAmount = $item->getRowTotal()*$discountPercent/100;
            $item->setDiscountAmount($discountAmount);
            $totalDiscount += $discountAmount;
        }
        $quote->setDiscountPercent($discountPercent)->setDiscountAmount($totalDiscount);
            
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