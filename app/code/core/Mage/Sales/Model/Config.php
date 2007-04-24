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
    
    public function getShippingConfig($vendor)
    {
        $config = Mage::getConfig()->getXml("global/salesShippingVendors/$vendor");
        return $config;
    }    
    
    public function getPaymentConfig($method)
    {
        $config = Mage::getConfig()->getXml("global/salesPaymentMethods/$method");
        return $config;
    }
}