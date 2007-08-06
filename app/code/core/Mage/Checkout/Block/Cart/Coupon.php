<?php

class Mage_Checkout_Block_Cart_Coupon extends Mage_Checkout_Block_Cart_Abstract 
{
    public function getQuote()
    {
        if (empty($this->_quote)) {
            $this->_quote = Mage::getSingleton('checkout/session')->getQuote();
        }
        return $this->_quote;
    }
    
    public function getCouponCode()
    {
        return $this->getQuote()->getCouponCode();
    }
    

}