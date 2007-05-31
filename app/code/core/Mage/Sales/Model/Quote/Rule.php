<?php

class Mage_Sales_Model_Quote_Rule extends Varien_Object
{
    public function __construct()
    {
        parent::__construct();
        $this->resetConditions();
        $this->resetActions();
    }
    
    public function getId()
    {
        return $this->getQuoteRuleId();
    }
    
    public function setId($id)
    {
        return $this->setQuoteRuleId($id);
    }
    
    public function resetConditions()
    {
        $conditions = Mage::getModel('sales', 'quote_rule_condition_combine');
        $conditions->setRule($this)->setId('1');
        $this->setConditions($conditions);

        $this->setConditionItemNumber(1);
        $this->setConditionAddressNumber(1);

        return $this;
    }
    
    public function resetActions()
    {
        $actions = Mage::getModel('sales', 'quote_rule_action_collection');
        $actions->setRule($this);
        $this->setActions($actions);
        
        return $this;
    }

    public function toString($format='')
    {
        $str = "Name: ".$this->getName()."\n"
            ."Start at: ".$this->getStartAt()."\n"
            ."Expire at: ".$this->getExpireAt()."\n"
            ."Coupon code: ".$this->getCouponCode()."\n"
            ."Customer registered: ".$this->getCustomerRegistered()."\n"
            ."Customer is new buyer: ".$this->getCustomerNewBuyer()."\n"
            ."Description: ".$this->getDescription()."\n\n"
            .$this->getConditions()->toStringRecursive()."\n\n"
            .$this->getActions()->toStringRecursive()."\n\n";
        return $str;
    }
    
    /**
     * Returns rule as an array for admin interface
     * 
     * Output example:
     * array(
     *   'name'=>'Example rule',
     *   'conditions'=>{condition_combine::toArray}
     *   'actions'=>{action_collection::toArray}
     * )
     * 
     * @return array
     */
    public function toArray(array $arrAttributes = array())
    {
        $out = array(
            'name'=>$this->getName(),
            'description'=>$this->getDescription,
            'conditions'=>$this->getConditions()->toArray(),
            'actions'=>$this->getActions()->toArray(),
        );
        
        return $out;
    }
    
    public function getResource()
    {
        return Mage::getModel('sales_resource', 'quote_rule');
    }
    
    public function load($ruleId)
    {
        $data = $this->getResource()->load($ruleId);
        if (empty($data)) {
            return $this;
        }
        $this->addData($data);
        
        $conditionsArr = unserialize($this->getConditionsSerialized());
        $this->getConditions()->loadArray($conditionsArr);
        
        $actionsArr = unserialize($this->getActionsSerialized());
        $this->getActions()->loadArray($actionsArr);
        
        return $this;
    }
    
    public function save()
    {
        $conditions = serialize($this->getConditions()->toArray());
        $this->setConditionsSerialized($conditions);

        $actions = serialize($this->getActions()->toArray());
        $this->setActionsSerialized($actions);
        
        $this->getResource()->save($this);
        
        return $this;
    }
    
    public function delete($ruleId=null)
    {
        if (is_null($ruleId)) {
            $ruleId = $this->getId();
        }
        
        if ($ruleId) {
            $this->getResource()->delete($ruleId);
        }
        return $this;
    }

}