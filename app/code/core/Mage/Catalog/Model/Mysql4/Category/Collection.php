<?php

class Mage_Catalog_Model_Mysql4_Category_Collection extends Varien_Data_Collection_Db 
{
    protected $_categoryTable;
    protected $_categoryProductTable;
    protected $_attributeTable;
    protected $_attributeValueTable;
    protected $_storeId;
    
    public function __construct() 
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('catalog_read'));
        $this->_categoryTable       = Mage::getSingleton('core/resource')->getTableName('catalog/category');
        $this->_categoryProductTable= Mage::getSingleton('core/resource')->getTableName('catalog/category_product');
        $this->_attributeTable      = Mage::getSingleton('core/resource')->getTableName('catalog/category_attribute');
        $this->_attributeValueTable = Mage::getSingleton('core/resource')->getTableName('catalog/category_attribute_value');;

        $this->_storeId = Mage::getSingleton('core/store')->getId();
               
        $this->_sqlSelect->from($this->_categoryTable);
        $this->_sqlSelect->join($this->_categoryProductTable, 
            "$this->_categoryProductTable.category_id=$this->_categoryTable.category_id", 'category_id');
        // TODO: dynamic add attribute
        $this->_sqlSelect->join($this->_attributeValueTable, 
            "$this->_attributeValueTable.category_id=$this->_categoryTable.category_id
            AND $this->_attributeValueTable.store_id=$this->_storeId
            AND $this->_attributeValueTable.attribute_id=1", 'attribute_value AS name');
            
        $this->setOrder($this->_categoryProductTable.'.position', 'asc');
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog/category'));
    }
    
    public function addProductFilter($productId)
    {
        $this->addFilter('product', $this->_conn->quoteInto($this->_categoryProductTable.'.product_id=?', $productId), 'string');
        return $this;
    }
}