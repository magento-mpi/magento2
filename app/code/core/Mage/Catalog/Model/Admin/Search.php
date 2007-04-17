<?php

class Mage_Catalog_Model_Admin_Search extends Varien_Data_Object 
{
    public function load()
    {
        $arr = array();
        
        if (!$this->getStart() || !$this->getLimit() || !$this->getQuery()) {
            return $arr;
        }
        
        
        
        return $arr;
    }
}