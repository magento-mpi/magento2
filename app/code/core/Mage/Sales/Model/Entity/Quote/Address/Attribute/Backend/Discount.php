<?php

class Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend_Discount
    extends Mage_Sales_Model_Entity_Quote_Address_Attribute_Backend
{
    public function collectTotals(Mage_Sales_Model_Quote_Address $address)
    {
        $coupon = Mage::getModel('sales/discount_coupon')->loadByCode($address->getCouponCode());
        #print_r($coupon); die;
        $address->setDiscountAmount(0);
        
        if (!empty($coupon) && $coupon->isValid()) {
            $coupon->setQuoteDiscount($address);
        } else {
            $address->setCouponCode('');
            $address->setDiscountPercent(0);
        }
            
        $address->setGrandTotal($address->getGrandTotal() - $address->getDiscountAmount());

        return $this;
    }


}