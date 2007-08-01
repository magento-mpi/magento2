<?php
/**
 * Quote rules collection
 *
 * @package    Mage
 * @subpackage Sales
 * @author     Moshe Gurvich (moshe@varien.com)
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Sales_Model_Mysql4_Quote_Rule_Collection extends Mage_Rule_Model_Mysql4_Rule_Collection
{

    /**
     * Initialize resource collection variables
     *
     */
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('sales_read'));
        
        $ruleTable = Mage::getSingleton('core/resource')->getTableName('sales/quote_rule');
        $this->_sqlSelect->from($ruleTable)->order('sort_order');
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('sales/quote_rule'));
    }
    
    /**
     * Retrieve environment for the rules in collection
     *
     * @return Mage_Sales_Model_Quote_Rule_Environment
     */
    public function getEnv()
    {
        if (!$this->_env) {
            $this->_env = Mage::getModel('sales/quote_rule_environment');
            $this->_env->collect();
        }
        return $this->_env;
    }
    
    /**
     * Set filter for the collection based on the environment
     *
     * @return Mage_Sales_Model_Mysql4_Quote_Rule_Collection
     */
    public function setActiveFilter()
    {
        parent::setActiveFilter();
        
        $e = $this->getEnv()->getData();
        
        if (!empty($e['coupon_code'])) {
            $this->_sqlSelect->where("coupon_code in ('', ?)", $e['coupon_code']);
        } else {
            $this->_sqlSelect->where("coupon_code=''");
        }
        
        if (!isset($e['customer_registered'])
            || !is_numeric($reg = $e['customer_registered']) && ($reg<0 || $reg>1)) {
            $reg = 2;
        }
        $this->_sqlSelect->where("customer_registered=?", $reg);
        
        if (!isset($e['customer_new_buyer'])
            || !is_numeric($new = $e['customer_new_buyer']) && ($new<0 || $new>1)) {
            $new = 2;
        }        
        $this->_sqlSelect->where("customer_new_buyer=?", $new);

        return $this;
    }
}