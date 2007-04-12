<?php

class Mage_Checkout_Block_Shipping_Method extends Mage_Core_Block_Template 
{
    function __construct()
    {
        parent::__construct();
        
        $checkout = Mage::getSingleton('checkout_model', 'session');
        $quote = $checkout->getQuote();
        
        if (!$checkout->getShippingMethods()) {
            $checkout->setShippingMethods($quote->collectShippingMethods());
        }
        $methods = $checkout->getShippingMethods();

        $selectedMethod = $quote->getAddressByType('shipping')->getAttribute('shipping_method');

        $this->setViewName('Mage_Checkout', 'onepage/shipping_method/box.phtml');
	    $this->assign('methods', $methods)->assign('selectedMethod', $selectedMethod);
    }
}