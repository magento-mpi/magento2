<?php


class Mage_SalesRule_Model_Mysql4_Rule_Customer extends Mage_Core_Model_Mysql4_Abstract 
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('salesrule/rule_customer', 'rule_customer_id');
    }
    
    public function loadByCustomerRule($rule, $customerId, $ruleId)
    {
    	$read = $this->getConnection('read');
    	$select = $read->select()->from($this->getMainTable())
    		->where('customer_id=?', $customerId)
    		->where('rule_id=?', $ruleId);
    	$rule->setData($read->fetchRow($select));
    	return $this;
    }
}