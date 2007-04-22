<?php

class Mage_Checkout_Block_Shipping_Method extends Mage_Core_Block_Template 
{
    function __construct()
    {
        parent::__construct();

        $checkout = Mage::getSingleton('checkout', 'session');
        $quote = $checkout->getQuote();

        $address = $quote->getAddressByType('shipping');

        if (!$checkout->getShippingMethods() && !empty($address)) {
            $methods = Mage::getModel('sales', 'shipping')->collectMethodsByAddress($address);
            $checkout->setShippingMethods($methods);
        } else {
            $methods = $checkout->getShippingMethods();
        }

        if (!empty($address)) {
            $selectedMethod = $address->getShippingMethod();
        } else {
            $selectedMethod = '';
        }

        $this->setTemplate('checkout/onepage/shipping_method/box.phtml');
	    $this->assign('methods', $methods)->assign('selectedMethod', $selectedMethod);
    }
}