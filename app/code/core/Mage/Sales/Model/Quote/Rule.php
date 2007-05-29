<?php

class Mage_Sales_Model_Price_Rule extends Varien_Object
{
    public function getId()
    {
        return $this->getQuoteRuleId();
    }
    
    public function setId($id)
    {
        return $this->setQuoteRuleId($id);
    }
    
    public function getResource()
    {
        return Mage::getModel('sales_resource', 'quote_rule');
    }
    
    public function load($ruleId)
    {
        $this->addData($this->getResource()->load($ruleId));
    }
    
    public function save()
    {
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