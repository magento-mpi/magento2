<?php

class Mage_Sales_Model_Quote_Attribute_Discount extends Mage_Sales_Model_Quote_Attribute
{
    public function collectTotals(Mage_Sales_Model_Quote $quote)
    {
        $quote->setDiscountAmount(0);

        $coupon = Mage::getModel('sales_resource', 'discount')->getCouponByCode($quote->getCouponCode());
        if ($coupon) {
            $quote->setDiscountPercent($coupon['discount_percent']);
            
            foreach ($quote->getEntitiesByType('item') as $item) {
                $item->setDiscountPercent($quote->getDiscountPercent());
                $item->setDiscountAmount($item->getRowTotal()*$item->getDiscountPercent()/100);
                $quote->setDiscountAmount($quote->getDiscountAmount()+$item->getDiscountAmount());
            }
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