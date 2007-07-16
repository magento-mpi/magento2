<?php

class Mage_Catalog_Model_Search extends Mage_Core_Model_Abstract 
{
    protected function _construct()
    {
        $this->_init('catalog/search');
    }
    
    public function updateSearch($query, $numResults)
    {
        $this->getResource()->loadByQuery($this, $query);
        if (!$this->getSearchId()) {
            $this->setSearchQuery($query);
        }
        $this->setNumResults($numResults);
        $this->setPopularity($this->getPopularity()+1);
        
        $this->save();
        
        return $this;
    }
}