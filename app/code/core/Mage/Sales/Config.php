<?php

class Mage_Sales_Config
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
}