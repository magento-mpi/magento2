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
        $config = Mage::getConfig()->getNode("global/sales/shipping/vendors/$vendor");
        return $config;
    }    
    
    public function getPaymentConfig($method)
    {
        $config = Mage::getConfig()->getNode("global/sales/payment/methods/$method");
        return $config;
    }
    
    public function getQuoteRuleConditionInstance($type)
    {
        $config = Mage::getConfig()->getNodeClassInstance("global/sales/quote/rule/conditions/$type");
        return $config;
    }
    
    public function getQuoteRuleActionInstance($type)
    {
        return Mage::getConfig()->getNodeClassInstance("global/sales/quote/rule/actions/$type");
    }
}