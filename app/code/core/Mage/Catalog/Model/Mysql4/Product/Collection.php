<?php

/**
 * Products collection
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Collection extends Varien_Data_Collection_Db 
{
    protected $_productTable;
    protected $_attributeTable;
    protected $_attributeTables;
    protected $_categoryProductTable;
    
    protected $_websiteId;
    protected $_isCategoryJoined=false;
    
    function __construct($config = array())
    {
        parent::__construct(Mage::registry('resources')->getConnection('catalog_read'));

        $this->_productTable   = Mage::registry('resources')->getTableName('catalog', 'product');
        $this->_categoryProductTable = Mage::registry('resources')->getTableName('catalog', 'category_product');
        $this->_attributeTable = Mage::registry('resources')->getTableName('catalog', 'product_attribute');
        $this->_attributeTables['varchar']  = Mage::registry('resources')->getTableName('catalog', 'product_attribute_varchar');
        $this->_attributeTables['text']     = Mage::registry('resources')->getTableName('catalog', 'product_attribute_text');
        $this->_attributeTables['decimal']  = Mage::registry('resources')->getTableName('catalog', 'product_attribute_decimal');
        $this->_attributeTables['int']      = Mage::registry('resources')->getTableName('catalog', 'product_attribute_int');
        $this->_attributeTables['date']     = Mage::registry('resources')->getTableName('catalog', 'product_attribute_date');

        $productColumns = new Zend_Db_Expr("SQL_CALC_FOUND_ROWS $this->_productTable.*");
        $this->_sqlSelect->from($this->_productTable, $productColumns);
       
        $this->setPageSize(9);
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog', 'product'));
        $this->setWebsiteId(Mage::registry('website')->getId());
    }
    
    /**
     * Add category condotion for collection
     *
     * @param int || array $category
     */
    function addCategoryFilter($category)
    {
        if (!$this->_isCategoryJoined) {
            $this->_sqlSelect->join(
                $this->_categoryProductTable, 
                new Zend_Db_Expr("$this->_categoryProductTable.product_id=$this->_productTable.product_id"),
                'product_id'
            );
        }
        
        if (is_array($category)) {
            $condition = $this->getConnection()->quoteInto("$this->_categoryProductTable.category_id IN (?)",$category);
        }
        else {
            $condition = $this->getConnection()->quoteInto("$this->_categoryProductTable.category_id=?",$category);
        }

        $this->addFilter('category', $condition, 'string');
        return $this;
    } 
    
    function addSearchFilter($query)
    {
        $query = trim(strip_tags($query));
        if (!empty($query)) {
            $condition = $this->getConnection()->quoteInto("(name_varchar.attribute_value LIKE ?)", "%$query%");
            $this->addFilter('search', $condition, 'string');
        }
        return $this;
    }

    /**
     * Set select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  Varien_Data_Collection_Db
     */
    public function setOrder($field, $direction = 'desc')
    {
        if ($field == 'product_id') {
            $field = Mage::registry('resources')->getTableName('catalog', 'product').'.'.$field;
        }
        return parent::setOrder($field, $direction);
    }
        
    function addAttributeToSelect($attributeCode, $attributeType, $attributeValue=false)
    {
        if (!isset($this->_attributeTables[$attributeType])) {
            Mage::exception('Wrong attribute type:'.$attributeType, 0, 'Mage_Catalog');
        }
        
        $attributeId = $this->getConnection()->fetchOne("SELECT attribute_id FROM $this->_attributeTable WHERE attribute_code=?", $attributeCode);
        $tableAlias= $attributeCode . '_' . $attributeType;
        $tableName = $this->_attributeTables[$attributeType] . ' AS ' . $tableAlias;
        
        $condition = "$tableAlias.product_id=$this->_productTable.product_id AND $tableAlias.attribute_id=$attributeId";
        
        if ($this->_websiteId) {
            $condition.= " AND $tableAlias.website_id=".(int) $this->_websiteId;
        }
        
        if ($attributeValue) {
            //$this->addFilter("$tableAlias.attribute_value", $attributeValue);
            $condition.= " AND $tableAlias.attribute_value='".$attributeValue."'";
        }
        
        $this->_sqlSelect->join($tableName, $condition, new Zend_Db_Expr("$tableAlias.attribute_value AS $attributeCode"));
        return $this;
    }
    
    public function setWebsiteId($websiteId)
    {
        $this->_websiteId = $websiteId;
        return $this;
    }
    
    public function getWebsiteId()
    {
        return $this->_websiteId;
    }
    
    public function getItemById($idValue)
    {
        foreach ($this->_items as $product) {
            if ($product->getProductId() == $idValue) { 
                return $product;
            }
        }
        return false;
    }
    
    /**
     * Load data
     * 
     * Redeclared for SELECT FOUND_ROWS()
     *
     * @return  Varien_Data_Collection_Db
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        parent::loadData($printQuery, $logQuery);
        $this->getSize();
        return $this;
    }

    /**
     * Get sql for get record count
     *
     * @return  string
     */
    public function getSelectCountSql()
    {
        return 'SELECT FOUND_ROWS()';
    }
    
    
}