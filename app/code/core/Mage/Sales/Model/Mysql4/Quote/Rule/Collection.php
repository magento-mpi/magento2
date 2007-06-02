<?php
/**
 * Quote rules collection
 *
 * @package    Mage
 * @subpackage Sales
 * @author     Moshe Gurvich (moshe@varien.com)
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Sales_Model_Mysql4_Quote_Rule_Collection extends Varien_Data_Collection_Db
{
    /**
     * Quote rule environment
     *
     * @var Mage_Sales_Model_Quote_Rule_Environment
     */
    protected $_env;
    
    /**
     * Initialize resource collection variables
     *
     */
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('sales_read'));
        
        $ruleTable = Mage::registry('resources')->getTableName('sales_resource', 'quote_rule');
        $this->_sqlSelect->from($ruleTable)->order('sort_order');
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('sales', 'quote_rule'));
    }
    
    /**
     * Set environment for all rules in collection
     *
     * @param Mage_Sales_Model_Quote_Rule_Environment $env
     * @return Mage_Sales_Model_Mysql4_Quote_Rule_Collection
     */
    public function setEnv(Mage_Sales_Model_Quote_Rule_Environment $env=null)
    {
        $this->_env = $env;
        return $this;
    }
    
    /**
     * Retrieve environment for the rules in collection
     *
     * @return Mage_Sales_Model_Quote_Rule_Environment
     */
    public function getEnv()
    {
        if (!$this->_env) {
            $this->_env = Mage::getModel('sales', 'quote_rule_environment');
            $this->_env->collect();
        }
        return $this->_env;
    }
    
    /**
     * Overload default addItem method to set environment for the rules
     *
     * @param Mage_Sales_Model_Quote_Rule $rule
     * @return Mage_Sales_Model_Mysql4_Quote_Rule_Collection
     */
    public function addItem(Mage_Sales_Model_Quote_Rule $rule)
    {
        $rule->setEnv($this->getEnv())->setIsCollectionValidated(true);
        parent::addItem($rule);
        return $this;
    }
    
    /**
     * Set filter for the collection based on the environment
     *
     * @return Mage_Sales_Model_Mysql4_Quote_Rule_Collection
     */
    public function setActiveFilter()
    {
        $e = $this->getEnv()->getData();
        
        $this->_sqlSelect->where("is_active=1");
        
        if (!empty($e['now'])) {
            if (!is_numeric($e['now'])) {
                $e['now'] = strtotime($e['now']);
            }
            $now = date("Y-m-d H:i:s", $e['now']);
        } else {
            $now = date("Y-m-d H:i:s");
        }
        $this->_sqlSelect->where("start_at<='$now' and expire_at>='$now'");
        
        if (!empty($e['coupon_code'])) {
            $this->_sqlSelect->where($this->_conn->quoteInto("coupon_code in ('', ?)", $e['coupon_code']));
        } else {
            $this->_sqlSelect->where("coupon_code=''");
        }
        
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
        
        if (isset($e['show_in_catalog'])) {
            $this->_sqlSelect->where("show_in_catalog=".(int)(bool)$e['show_in_catalog']);
        }
        
        return $this;
    }
    
    /**
     * Process the quote with all the rules in collection
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return Mage_Sales_Model_Mysql4_Quote_Rule_Collection
     */
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