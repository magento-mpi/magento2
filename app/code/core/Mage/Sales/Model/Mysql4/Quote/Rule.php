<?php

class Mage_Sales_Model_Mysql4_Quote_Rule
{
    /**
     * Read resource adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_read;
    
    /**
     * Write resource adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_write;
    
    /**
     * Rule table name
     *
     * @var string
     */
    protected $_ruleTable;
    
    public function __construct()
    {
        $this->_read = Mage::registry('resources')->getConnection('sales_read');
        $this->_write = Mage::registry('resources')->getConnection('sales_write');
        $this->_ruleTable = Mage::registry('resources')->getTableName('sales_resource', 'quote_rule');
    }
    
    public function load($ruleId)
    {
        $row = $this->_read->fetchRow("select * from $this->_ruleTable where quote_rule_id=?", array($ruleId));
        return $row;
    }
    
    public function save(Mage_Sales_Model_Quote_Rule $rule)
    {
        $data = $rule->__toArray(array('quote_rule_id', 'name', 'description', 'is_active', 'start_at', 'expire_at', 'coupon_code', 'customer_registered', 'customer_new_buyer', 'show_in_catalog', 'sort_order', 'conditions_serialized', 'actions_serialized'));
        
        if ($rule->getId()) {
            $condition = $this->_write->quoteInto("quote_rule_id=?", $rule->getId());
            $this->_write->update($this->_ruleTable, $data, $condition);
        } else {
            $this->_write->insert($this->_ruleTable, $data);
            $rule->setId($this->_write->lastInsertId());
        }
        return $this;
    }
    
    public function delete($ruleId)
    {
        $condition = $this->_write->quoteInto("quote_rule_id=?", $ruleId);
        $this->_write->delete($this->_ruleTable, $condition);
        return $this;
    }
}