<?php

class Mage_CatalogRule_Model_Mysql4_Rule_Product_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    protected function _construct()
    {
        $this->_init('catalogrule/rule_product');
    }
}