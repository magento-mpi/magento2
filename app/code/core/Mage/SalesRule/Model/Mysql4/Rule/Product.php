<?php


class Mage_SalesRule_Model_Mysql4_Rule_Product extends Mage_Core_Model_Mysql4_Abstract 
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('salesrule/rule_product', 'product_rule_id');
    }
}