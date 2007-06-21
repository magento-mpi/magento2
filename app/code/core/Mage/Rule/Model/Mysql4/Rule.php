<?php

/**
 * Quote rule mysql4 resource model
 *
 * @package    Mage
 * @subpackage Rule
 * @author     Moshe Gurvich (moshe@varien.com)
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Rule_Model_Mysql4_Rule
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
    
    protected $_ruleIdField;
    
    protected $_ruleTableFields;
    
    /**
     * Initialize rule resource variables
     *
     * Example:
     */
    /*
    public function __construct()
    {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('sales_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('sales_write');
        $this->_ruleTable = Mage::getSingleton('core/resource')->getTableName('sales/quote_rule');
        
        $this->_ruleIdField = 'quote_rule_id';
        $this->_ruleTableFields = array('quote_rule_id', 'name', 'description', 'is_active', 'start_at', 'expire_at', 'coupon_code', 'customer_registered', 'customer_new_buyer', 'show_in_catalog', 'sort_order', 'conditions_serialized', 'actions_serialized');
    }
    */
    
    /**
     * Load rule by id
     *
     * @param integer $ruleId
     * @return array
     */
    public function load($ruleId)
    {
        $row = $this->_read->fetchRow("select * from $this->_ruleTable where $this->_ruleIdField=?", array($ruleId));
        return $row;
    }
    
    /**
     * Save the rule from object
     *
     * @param Mage_Rule_Model_Abstract $rule
     * @return Mage_Rule_Model_Mysql4_Rule
     */
    public function save(Mage_Rule_Model_Abstract $rule)
    {
        $data = $rule->__toArray($this->_ruleTableFields);
        
        if ($rule->getId()) {
            $condition = $this->_write->quoteInto("$this->_ruleIdField=?", $rule->getId());
            $this->_write->update($this->_ruleTable, $data, $condition);
        } else {
            $this->_write->insert($this->_ruleTable, $data);
            $rule->setId($this->_write->lastInsertId());
        }
        return $this;
    }
    
    /**
     * Delete the rule by id
     *
     * @param integer $ruleId
     * @return Mage_Rule_Model_Mysql4_Rule
     */
    public function delete($ruleId)
    {
        $condition = $this->_write->quoteInto("$this->_ruleIdField=?", $ruleId);
        $this->_write->delete($this->_ruleTable, $condition);
        return $this;
    }
}