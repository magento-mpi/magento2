<?php

/**
 * Quote rule mysql4 resource model
 *
 * @package    Mage
 * @subpackage Sales
 * @author     Moshe Gurvich (moshe@varien.com)
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Sales_Model_Mysql4_Quote_Rule extends Mage_Rule_Model_Mysql4_Rule
{

    public function __construct()
    {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('sales_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('sales_write');
        $this->_ruleTable = Mage::getSingleton('core/resource')->getTableName('sales/quote_rule');
        
        $this->_ruleIdField = 'quote_rule_id';
        $this->_ruleTableFields = array('quote_rule_id', 'name', 'description', 'is_active', 'start_at', 'expire_at', 'coupon_code', 'customer_registered', 'customer_new_buyer', 'sort_order', 'conditions_serialized', 'actions_serialized');
    }
}