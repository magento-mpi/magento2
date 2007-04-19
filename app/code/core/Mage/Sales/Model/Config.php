<?php

class Mage_Sales_Model_Config
{
    public function getShippingOrig()
    {
        $orig = array(
            'orig_country_id'=>223,
            'orig_region_id'=>1,
            'orig_postcode'=>'90034',
        );
        return $orig;
    }
    
    public function getShippingDefaults($vendor)
    {
        $config = Mage::getConfig()->getXml()->global->salesShippingVendors->$vendor;
        return $config->defaults;
    }    
    
    public function getPaymentDefaults($method)
    {
        $config = Mage::getConfig()->getXml()->global->salesPayment->$method;
        return $config->defaults;
    }
}