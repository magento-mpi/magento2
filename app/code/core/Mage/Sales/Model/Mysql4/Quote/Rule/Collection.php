<?php
/**
 * Customers collection
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Sales_Model_Mysql4_Quote_Rule_Collection extends Varien_Data_Collection_Db
{
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('sales_read'));
        
        $ruleTable = Mage::registry('resources')->getTableName('sales_resource', 'quote_rule');
        $this->_sqlSelect->from($ruleTable)
            ->where("is_active=1 and start_at<=now() and expire_at>=now()")
            ->order('sort_order');
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('sales', 'quote_rule'));
    }
    
    public function setActiveFilter(array $params)
    {
        if (!empty($params['coupon'])) {
            $this->_sqlSelect->where($this->_conn->quoteInto("coupon_code in ('', ?)", $params['coupon']);
        } else {
            $this->_sqlSelect->where("coupon_code=''");
        }
        
        if (!isset($params['customer_registered'])) 
            || !is_numeric($reg = $params['customer_registered']) && ($reg<0 || $reg>1)) {
            $reg = 2;
        }
        $this->_sqlSelect->where("customer_registered=".$reg);
        
        if (!isset($params['customer_new_buyer'])
            || !is_numeric($new = $params['customer_new_buyer']) && ($new<0 || $new>1)) {
            $new = 2;
        }        
        $this->_sqlSelect->where("customer_new_buyer=".$new);
        
        if (isset($params['show_in_catalog'])) {
            $this->_sqlSelect->where("show_in_catalog=".(int)(bool)$params['show_in_catalog']);
        }
        
        return $this;
    }
    
    public function processQuote(Mage_Sales_Model_Quote $quote)
    {
        $rules = $this->getItems();
        foreach ($rules as $rule) {
            $rule->processQuote($quote);
            if ($rule->getStopProcessingRules()) {
                break;
            }
        }
        return $this;
    }
}