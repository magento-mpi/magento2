<?php

class Mage_Catalog_Model_Mysql4_Search_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    protected function _construct()
    {
        $this->_init('catalog/search');
    }
}