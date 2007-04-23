<?php

class Mage_Checkout_Block_Shipping_Method extends Mage_Core_Block_Template 
{
    public function __construct()
    {
        parent::__construct();

        $quote = Mage::getSingleton('checkout', 'session')->getQuote();

        $address = $quote->getAddressByType('shipping');

        $methodEntities = $quote->getEntitiesByType('shipping'); 
        if (!empty($methodEntities) && !empty($address)) {
            $estimateFilter = new Varien_Filter_Object_Grid();
            $estimateFilter->addFilter(new Varien_Filter_Sprintf('$%s', 2), 'amount');
            $methods = $estimateFilter->filter($methodEntities);
            $selectedMethod = $quote->getShippingMethod();
            $this->assign('methods', $methods)->assign('selectedMethod', $selectedMethod);
        } else {
            $this->assign('methods', array())->assign('selectedMethod', '');
        }

        $this->setTemplate('checkout/onepage/shipping_method/available.phtml');
	    
    }
}