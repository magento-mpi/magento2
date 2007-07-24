<?php
/**
 * Abstract rules collection to be extended
 *
 * @package    Mage
 * @subpackage Rule
 * @author     Moshe Gurvich (moshe@varien.com)
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Rule_Model_Mysql4_Rule_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    /**
     * Quote rule environment
     *
     * @var Mage_Rule_Model_Environment
     */
    protected $_env;
    
    protected function _construct()
    {
    	$this->_init('rule/rule')
    }
    
    /**
     * Initialize resource collection variables
     *
     * Example:
     */
    /*
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('sales_read'));
        
        $ruleTable = Mage::getSingleton('core/resource')->getTableName('sales/quote_rule');
        $this->_sqlSelect->from($ruleTable)->order('sort_order');
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('sales/quote_rule'));
    }
    */
    
    /**
     * Set environment for all rules in collection
     *
     * @param Mage_Rule_Model_Environment $env
     * @return Mage_Rule_Model_Mysql4_Rule_Collection
     */
    public function setEnv(Mage_Rule_Model_Environment $env=null)
    {
        $this->_env = $env;
        return $this;
    }
    
    /**
     * Retrieve environment for the rules in collection
     *
     * @return Mage_Rule_Model_Mysql4_Rule_Collection
     */
    public function getEnv()
    {
        if (!$this->_env) {
            $this->_env = Mage::getModel('core/rule_environment');
            $this->_env->collect();
        }
        return $this->_env;
    }
    
    /**
     * Overload default addItem method to set environment for the rules
     *
     * @param Mage_Rule_Model_Abstract $rule
     * @return Mage_Rule_Model_Mysql4_Rule_Collection
     */
    public function addItem(Mage_Rule_Model_Abstract $rule)
    {
        $rule->setEnv($this->getEnv())->setIsCollectionValidated(true);
        parent::addItem($rule);
        return $this;
    }
    
    /**
     * Set filter for the collection based on the environment
     *
     * @return Mage_Rule_Model_Mysql4_Rule_Collection
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

        return $this;
    }
    
    /**
     * Process the quote with all the rules in collection
     *
     * @return Mage_Rule_Model_Mysql4_Rule_Collection
     */
    public function process()
    {
        $rules = $this->getItems();
        foreach ($rules as $rule) {
            $rule->process();
            if ($rule->getStopProcessingRules()) {
                break;
            }
        }
        return $this;
    }
}