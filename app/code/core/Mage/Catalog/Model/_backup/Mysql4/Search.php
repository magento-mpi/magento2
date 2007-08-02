<?php

class Mage_Catalog_Model_Mysql4_Search extends Mage_Core_Model_Mysql4_Abstract 
{
    protected function _construct()
    {
        $this->_init('catalog/search', 'search_id');
    }
    
    public function loadByQuery($object, $query)
    {
        $this->load($object, $query, 'search_query');
    }
}