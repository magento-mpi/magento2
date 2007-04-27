<?php

class Mage_Catalog_Model_Mysql4_Product_Link_Collection extends Varien_Data_Collection_Db 
{
    protected $_linkTable;
    
    public function __construct() 
    {
        parent::__construct(Mage::registry('resources')->getConnection('catalog_read'));
        
        $this->_linkTable = Mage::registry('resources')->getTableName('catalog_resource', 'product_link');
        
        $this->_sqlSelect->from($this->_linkTable);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog', 'product_link'));
    }
    
    public function addProductFilter($condition)
    {
        $this->_sqlSelect->where($this->_getConditionSql("$this->_linkTable.product_id", $condition));
        return $this;
    }
    
    public function addTypeFilter($condition)
    {
        $this->_sqlSelect->where($this->_getConditionSql("$this->_linkTable.link_type_id", $condition));
        return $this;
    }
    
    public function loadData($printQuery=false, $logQuery=false)
    {
        if (!parent::loadData($printQuery, $logQuery)) {
            return $this;
        }
        
        $linkedProducts = Mage::getModel('catalog_resource', 'product_collection')
            ->addProductFilter(array('in'=>$this->getColumnValues('linked_product_id')))
            ->loadData();
            
        foreach ($this->getItems() as $item) {
            $item->setLinkedProduct($linkedProducts->getItemById($item->getLinkedProductId()));
        }
        return $this;
    }
}