<?php

class Mage_CatalogSearch_Model_Search extends Mage_Core_Model_Abstract 
{
    protected function _construct()
    {
        $this->_init('catalogsearch/search');
    }
    
    public function loadByQuery($query)
    {
    	$this->getResource()->loadByQuery($this, $query);
    	return $this;
    }
    
    public function updateSearch($query, $numResults=null)
    {
        $this->getResource()->loadByQuery($this, $query);
        if (!$this->getSearchId()) {
            $this->setSearchQuery($query);
        }
        if (!is_null($numResults)) {
        	$this->setNumResults($numResults);
        }
        $this->setPopularity($this->getPopularity()+1);
        
        $this->save();
        
        return $this;
    }
    
    public function getResourceCollection()
    {
    	return Mage::getResourceModel('catalogsearch/search_collection');
    }
}