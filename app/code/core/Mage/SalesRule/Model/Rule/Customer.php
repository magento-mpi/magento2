<?php

class Mage_SalesRule_Model_Rule_Customer extends Mage_Core_Model_Abstract 
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('salesrule/rule_product');
    }
}