<?php

class Mage_CatalogSearch_Model_Mysql4_Search_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    protected function _construct()
    {
        $this->_init('catalogsearch/search');
    }
    
    public function setQueryFilter($query)
    {
    	$this->getSelect()->reset(Zend_Db_Select::FROM)->distinct(true)
    		->from(
    			array('main_table'=>$this->getTable('catalogsearch/search')), 
    			array('search_query'=>"if(ifnull(synonim_for,'')<>'',synonim_for,search_query)", 'num_results')
    		)
    		->where('num_results>0 and search_query like ?', $query.'%')
    		->order('popularity desc');
print_r($this->getSelect()->__toString());
		return $this;
    }
}