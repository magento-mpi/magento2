<?php

/**
 * Products collection
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Collection extends Mage_Core_Model_Collection 
{
    protected $_productTable;
    protected $_attributeTable;
    protected $_attributeTables;
    protected $_categoryProductTable;
    
    function __construct($config = array())
    {
        parent::__construct(Mage::getModel('catalog'));

        $this->_productTable   = $this->_dbModel->getTableName('catalog_read', 'product');
        $this->_categoryProductTable = $this->_dbModel->getTableName('catalog_read', 'category_product');
        $this->_attributeTable = $this->_dbModel->getTableName('catalog_read', 'product_attribute');
        $this->_attributeTables['varchar']  = $this->_dbModel->getTableName('catalog_read', 'product_attribute_varchar');
        $this->_attributeTables['text']     = $this->_dbModel->getTableName('catalog_read', 'product_attribute_text');
        $this->_attributeTables['decimal']  = $this->_dbModel->getTableName('catalog_read', 'product_attribute_decimal');
        $this->_attributeTables['int']      = $this->_dbModel->getTableName('catalog_read', 'product_attribute_int');
        $this->_attributeTables['date']     = $this->_dbModel->getTableName('catalog_read', 'product_attribute_date');

        $productColumns = new Zend_Db_Expr("SQL_CALC_FOUND_ROWS $this->_productTable.*");
        $this->_sqlSelect->from($this->_productTable, $productColumns);
        $this->_sqlSelect->join(
            $this->_categoryProductTable, 
            new Zend_Db_Expr("$this->_categoryProductTable.product_id=$this->_productTable.product_id"),
            'product_id'
        );
       
        $this->setPageSize(9);
        $this->setItemObjectClass('Mage_Catalog_Model_Mysql4_Product');
    }
    
    /**
     * Add category condotion for collection
     *
     * @param int || array $category
     */
    function addCategoryFilter($category)
    {
        if (is_array($category)) {
            $condition = $this->_dbModel->getReadConnection()->quoteInto("$this->_categoryProductTable.category_id IN (?)",$category);
        }
        else {
            $condition = $this->_dbModel->getReadConnection()->quoteInto("$this->_categoryProductTable.category_id=?",$category);
        }

        $this->addFilter('category', $condition, 'string');
    } 
    
    function addSearchFilter($query)
    {
        $query = trim(strip_tags($query));
        if (!empty($query)) {
            $condition = $this->_dbModel->getReadConnection()->quoteInto("(name LIKE ? OR description LIKE ?)", "%$query%");
            $this->addFilter('search', $condition, 'string');
        }
        return $this;
    }

    /**
     * Set select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  Mage_Core_Model_Collection
     */
    public function setOrder($field, $direction = 'desc')
    {
        if ($field == 'product_id') {
            $field = $this->_dbModel->getTableName('catalog_read', 'product').'.'.$field;
        }
        return parent::setOrder($field, $direction);
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
    
    function addAttributeToSelect($attributeCode, $attributeType)
    {
        if (!isset($this->_attributeTables[$attributeType])) {
            Mage::exception('Wrong attribute type:'.$attributeType, 0, 'Mage_Catalog');
        }
        $attributeId = $this->_dbModel->getReadConnection()->fetchOne("SELECT attribute_id FROM $this->_attributeTable WHERE attribute_code=?", $attributeCode);
        $tableAlias= $attributeCode . '_' . $attributeType;
        //$tableName = new Zend_Db_Expr($this->_attributeTables[$attributeType] . ' AS ' . $tableAlias);
        $tableName = $this->_attributeTables[$attributeType] . ' AS ' . $tableAlias;
        
        $condition = "$tableAlias.product_id=$this->_productTable.product_id AND $tableAlias.attribute_id=$attributeId AND $tableAlias.website_id=".Mage_Core_Environment::getCurentWebsite();
        $this->_sqlSelect->join($tableName, $condition, new Zend_Db_Expr("$tableAlias.attribute_value AS $attributeCode"));
    }
}