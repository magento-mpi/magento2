<?php

class Mage_Catalog_Model_Config
{
    public function getProductRuleConditionInstance($type)
    {
        return Mage::getConfig()->getNodeClassInstance("global/catalog/product/rule/conditions/$type");
    }
    
    public function getProductRuleActionInstance($type)
    {
        return Mage::getConfig()->getNodeClassInstance("global/catalog/product/rule/actions/$type");
    }
}