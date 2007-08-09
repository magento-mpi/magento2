<?php

class Mage_CatalogRule_Model_Rule_Product_Price extends Mage_Core_Model_Abstract 
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('catalogrule/rule_product_price');
    }
}