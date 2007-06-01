<?php

class Mage_Sales_Model_Quote_Rule_Condition_Quote extends Mage_Sales_Model_Quote_Rule_Condition_Abstract
{
    public function loadAttributes()
    {
        $this->setAttributeOption(array(
            'coupon_code'=>'Coupon code',
            'subtotal'=>'Subtotal',
            'currency_code'=>'Currency',
            'shipping_amount'=>'Shipping amount',
            'shipping_method'=>'Shipping method',
            'discount_amount'=>'Discount amount',
            'discount_percent'=>'Discount percent',
            'weight'=>'Weight',
        ));
        return $this;
    }
    
    public function toString($format='')
    {
        return 'Cart '.parent::toString();
    }
    
    public function validateQuote(Mage_Sales_Model_Quote $quote)
    {
        return $this->validateAttribute($quote->getData($this->getAttribute()));
    }
}