<?php

class Mage_Sales_Model_Quote_Rule extends Mage_Rule_Model_Rule
{
	protected function _construct()
	{
		parent::_construct();
	}
	
    public function getId()
    {
        return $this->getQuoteRuleId();
    }
    
    public function setId($id)
    {
        return $this->setQuoteRuleId($id);
    }
    
    public function getEnv()
    {
        if (!$this->getData('env')) {
            $this->setData('env', Mage::getModel('rule/environment'));
        }
        return $this->getData('env');
    }
    
    public function resetConditions(Mage_Rule_Model_Condition_Interface $conditions=null)
    {
        parent::resetConditions(Mage::getModel('rule/condition_combine'));

        $this->setFoundQuoteItemNumber(1);
        $this->setFoundQuoteItems(array());
        
        $this->setFoundQuoteAddressNumber(1);
        $this->setFoundQuoteAddresses(array());
        
        return $this;
    }
    
    public function getConditionInstance($type)
    {
        return Mage::getSingleton('sales/config')->getQuoteRuleConditionInstance($type);
    }
    
    public function resetActions(Mage_Rule_Model_Action_Interface $actions=null)
    {
        parent::resetActions(Mage::getModel('rule/action_collection'));
        
        return $this;
    }
    
    public function getActionInstance($type)
    {
        return Mage::getSingleton('sales/config')->getQuoteRuleActionInstance($type);
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
        $out = parent::toArray($arrAttributes);
        $out['coupon_code'] = $this->getCouponCode();
        $out['customer_registered'] = $this->getCustomerRegistered();
        $out['customer_new_buyer'] = $this->getCustomerNewBuyer();
        
        return $out;
    }
    
    public function validate()
    {
        if (!$this->getIsCollectionValidated()) {
            $env = $this->getEnv();
            $result = $result && $this->getIsActive()
                && (strtotime($this->getStartAt()) <= $env->getNow())
                && (strtotime($this->getExpireAt()) >= $env->getNow())
                && ($this->getCouponCode()=='' || $this->getCouponCode()==$env->getCouponCode())
                && ($this->getCustomerRegistered()==2 || $this->getCustomerRegistered()==$env->getCustomerRegistered())
                && ($this->getCustomerNewBuyer()==2 || $this->getCustomerNewBuyer()==$env->getCustomerNewBuyer());
            if (!$result) {
                return false;
            }
        }

        return parent::validate();
    }
}