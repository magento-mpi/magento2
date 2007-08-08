<?php

class Mage_CatalogRule_Model_Rule extends Mage_Rule_Model_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init('catalogrule/rule');
    }
    
    public function getId()
    {
        return $this->getProductRuleId();
    }
    
    public function setId($id)
    {
        return $this->setProductRuleId($id);
    }
    
    public function getEnv()
    {
        if (!$this->getData('env')) {
            $this->setData('env', Mage::getModel('catalog/product_rule_environment'));
        }
        return $this->getData('env');
    }
    
    public function resetConditions()
    {
        parent::resetConditions(Mage::getModel('catalog/product_rule_condition_combine'));
        
        return $this;
    }
    
    public function getConditionInstance($type)
    {
        return Mage::getSingleton('catalog/config')->getProductRuleConditionInstance($type);
    }
    
    public function resetActions()
    {
        parent::resetActions(Mage::getModel('catalog/product_rule_action_collection'));
        
        return $this;
    }
    
    public function getActionInstance($type)
    {
        return Mage::getSingleton('catalog/config')->getProductRuleActionInstance($type);
    }

    public function toString($format='')
    {
        $str = "Name: ".$this->getName()."\n"
            ."Start at: ".$this->getStartAt()."\n"
            ."Expire at: ".$this->getExpireAt()."\n"
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
        $out['customer_registered'] = $this->getCustomerRegistered();
        $out['customer_new_buyer'] = $this->getCustomerNewBuyer();
        
        return $out;
    }
    
    public function processProduct(Mage_Catalog_Model_Product $product)
    {
        $this->validateProduct($product) && $this->updateProduct($product);
        return $this;
    }
    
    public function validateProduct(Mage_Catalog_Model_Product $product)
    {
        if (!$this->getIsCollectionValidated()) {
            $env = $this->getEnv();
            $result = $result && $this->getIsActive()
                && (strtotime($this->getStartAt()) <= $env->getNow())
                && (strtotime($this->getExpireAt()) >= $env->getNow())
                && ($this->getCustomerRegistered()==2 || $this->getCustomerRegistered()==$env->getCustomerRegistered())
                && ($this->getCustomerNewBuyer()==2 || $this->getCustomerNewBuyer()==$env->getCustomerNewBuyer())
                && $this->getConditions()->validateProduct($product);
        } else {
            $result = $this->getConditions()->validateProduct($product);
        }

        return $result;
    }
    
    public function updateProduct(Mage_Sales_Model_Product $product)
    {
        $this->getActions()->updateProduct($product);
        return $this;
    }
    
    public function getResourceCollection()
    {
        return Mage::getResourceModel('catalogrule/rule_collection');
    }
}