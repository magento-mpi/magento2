<?php

/**
 * Product rule mysql4 resource model
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Moshe Gurvich (moshe@varien.com)
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Rule extends Mage_Rule_Model_Mysql4_Rule
{

    public function __construct()
    {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('catalog_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('catalog_write');
        $this->_ruleTable = Mage::getSingleton('core/resource')->getTableName('catalog_resource', 'product_rule');
        
        $this->_ruleIdField = 'product_rule_id';
        $this->_ruleTableFields = array('product_rule_id', 'name', 'description', 'is_active', 'start_at', 'expire_at', 'customer_registered', 'customer_new_buyer',  'sort_order', 'conditions_serialized', 'actions_serialized');
    }
}