<?php
/**
 * Product rules collection
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Moshe Gurvich (moshe@varien.com)
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Rule_Collection extends Mage_Rule_Model_Mysql4_Rule_Collection
{

    /**
     * Initialize resource collection variables
     *
     */
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('catalog_read'));
        
        $ruleTable = Mage::getSingleton('core/resource')->getTableName('catalog_resource', 'product_rule');
        $this->_sqlSelect->from($ruleTable)->order('sort_order');
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog/product_rule'));
    }
    
    /**
     * Retrieve environment for the rules in collection
     *
     * @return Mage_Catalog_Model_Mysql4_Product_Rule_Collection
     */
    public function getEnv()
    {
        if (!$this->_env) {
            $this->_env = Mage::getModel('catalog/product_rule_environment');
            $this->_env->collect();
        }
        return $this->_env;
    }
    
    /**
     * Set filter for the collection based on the environment
     *
     * @return Mage_Catalog_Model_Mysql4_Product_Rule_Collection
     */
    public function setActiveFilter()
    {
        parent::setActiveFilter();
        
        $e = $this->getEnv()->getData();
        
        if (!isset($e['customer_registered'])
            || !is_numeric($reg = $e['customer_registered']) && ($reg<0 || $reg>1)) {
            $reg = 2;
        }
        $this->_sqlSelect->where("customer_registered=".$reg);
        
        if (!isset($e['customer_new_buyer'])
            || !is_numeric($new = $e['customer_new_buyer']) && ($new<0 || $new>1)) {
            $new = 2;
        }        
        $this->_sqlSelect->where("customer_new_buyer=".$new);
        
        return $this;
    }
}