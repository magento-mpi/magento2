<?php

class Mage_Sales_Model_Config
{
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