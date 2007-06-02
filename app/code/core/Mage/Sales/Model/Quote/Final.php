<?php

class Mage_Sales_Model_Quote_Final extends Mage_Sales_Model_Quote
{
    protected function _setDocumentProperties()
    {
        $this->_docType = 'quote_final';
    }

    /**
     * 
     * @todo move createOrders method here
     *
     */
    public function createOrders()
    {
        return parent::createOrders();
    }
    
    public function applyRules(array $envArr=array())
    {
        $env = Mage::getModel('sales', 'quote_rule_environment');
        $env->addData($envArr);

        $rules = Mage::getSingleton('sales_resource', 'quote_rule_collection');
        
        $rules->setEnv($env)->setActiveFilter($params)->loadData()->processQuote($this);
        
        return $this;
    }
    
}