<?php

class Mage_Sales_Model_Quote_Processed extends Mage_Sales_Model_Quote
{
    protected $_origQuote = null;
    
    protected function _setDocumentProperties()
    {
        $this->_docType = 'quote_processed';
    }
    
    public function setOrigQuote(Mage_Sales_Model_Quote $origQuote)
    {
        $this->_origQuote = $origQuote;
        return $this;
    }
    
    public function getOrigQuote()
    {
        if (!$this->_origQuote) {
            $origQuote = Mage::getModel('sales', 'quote')->load($this->getOrigQuoteId());
            $this->setOrigQuote($origQuote);
        }
        return $this->_origQuote;
    }
    
    public function importQuote(Mage_Sales_Model_Quote $origQuote) 
    {
        $this->setOrigQuote($origQuote);
        $this->setData($origQuote->getData());
        $this->_entitiesById = $origQuote->getEntitiesById();
        $this->_entitiesByType = $origQuote->getEntitiesByType();
        $this->setOrigQuoteId($origQuote->getId());
        $origQuote->setProcessedQuoteId($this->getId());
        foreach ($this->getEntitiesByType('items') as $item) {
            $item->setOrigEntityId($item->getEntityId());
        }
        $this->applyRules();
        return $this;
    }
    
    public function applyRules(array $envArr=array())
    {
        $env = Mage::getModel('sales', 'quote_rule_environment');
        $env->addData($envArr);

        $rules = Mage::getSingleton('sales_resource', 'quote_rule_collection');
        
        $rules->setEnv($env)->setActiveFilter()->loadData()->processQuote($this);
        
        return $this;
    }
    
    public function updateItems(array $itemsArr)
    {
        
    }

}