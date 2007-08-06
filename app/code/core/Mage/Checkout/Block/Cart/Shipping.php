<?php

class Mage_Checkout_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Abstract 
{
    public function getQuote()
    {
        if (empty($this->_quote)) {
            $this->_quote = Mage::getSingleton('checkout/session')->getQuote();
        }
        return $this->_quote;
    }
    
    public function getEstimateRates()
    {
        $rates = $this->getQuote()->getShippingAddress()->getAllShippingRates();
        $ratesFilter = new Varien_Filter_Object_Grid();
        $ratesFilter->addFilter($this->_priceFilter, 'price');
        return $ratesFilter->filter($rates);
    }
    
    public function getEstimatePostcode()
    {
        return $this->getQuote()->getShippingAddress()->getPostcode();
    }    
    
    public function getEstimateMethod()
    {
        return $this->getQuote()->getShippingAddress()->getShippingMethod();
    }
    
}