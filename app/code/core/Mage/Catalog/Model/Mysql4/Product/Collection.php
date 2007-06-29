<?php

/**
 * Products collection
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product_Collection extends Varien_Data_Collection_Db 
{
    protected $_productTable;
    protected $_categoryProductTable;
    protected $_tagRelTable;
    protected $_tagTable;
    
    protected $_storeId;
    protected $_isCategoryJoined=false;
    protected $_isLinkJoined=false;
    
    /**
     * All attributes collection
     *
     * @var Varin_Data_Collection_Db
     */
    protected $_attributes;
    
    protected $_joinedAttributes;
    
    function __construct($config = array())
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('catalog_read'));
        $this->_attributes = Mage::getResourceModel('catalog/product_attribute_collection')->load();
        
        $this->_productTable        = Mage::getSingleton('core/resource')->getTableName('catalog/product');
        $this->_categoryProductTable= Mage::getSingleton('core/resource')->getTableName('catalog/category_product');
		$this->_tagRelTable			= Mage::getSingleton('core/resource')->getTableName('tag/tag_relations');
		$this->_tagTable			= Mage::getSingleton('core/resource')->getTableName('tag/tag');
		
        $this->_sqlSelect->from($this->_productTable, new Zend_Db_Expr("SQL_CALC_FOUND_ROWS $this->_productTable.*"));
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog/product'));
        $this->setStoreId(Mage::getSingleton('core/store')->getId());
    }
    
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }
    
    public function getStoreId()
    {
        return $this->_storeId;
    }
    
    /**
     * Join attribute value table for select product collection
     *
     * @param string $attributeCode
     * @param mixed $attributeValue
     * @return this
     */
    function addAttributeToSelect($attributeCode, $attributeValue=false)
    {
        return $this->_joinAttributeTable($attributeCode, $attributeValue);
    }
    
    protected function _joinAttributeTable($attributeCode, $attributeValue=false, $comparisonType='=')
    {
        $attribute = $this->_attributes->getItemByCode($attributeCode);
        if ($attribute->isEmpty()) {
            throw Mage::exception('Mage_Catalog', 'Attribute with code "'.$attributeCode .' do not exist');
        }
        
        if (!$this->_isAttributeJoined($attribute)) {
            $this->_sqlSelect->join($attribute->getSelectTable(), $this->_getAttributeJoinCondition($attribute), $attribute->getTableColumns());
            $this->_joinedAttributes[$attribute->getCode()] = array();
        }
        
        //
        if ( $attributeValue !== false && (!isset($this->_joinedAttributes[$attribute->getCode()][$comparisonType]) 
             || $this->_joinedAttributes[$attribute->getCode()][$comparisonType] != $attributeValue) ) {
            
            $condition = $this->_conn->quoteInto($attribute->getTableAlias().".attribute_value $comparisonType ?", $attributeValue); 
            $this->_sqlSelect->where($condition);
            $this->_joinedAttributes[$attribute->getCode()][$comparisonType] = $attributeValue;
        }
        
        return $this;
    }
    
    protected function _isAttributeJoined(Mage_Catalog_Model_Product_Attribute $attribute)
    {
        return isset($this->_joinedAttributes[$attribute->getCode()]);
    }
    
    protected function _getAttributeJoinCondition(Mage_Catalog_Model_Product_Attribute $attribute)
    {
        $condition = $attribute->getTableAlias().".product_id=$this->_productTable.product_id AND ".
                     $attribute->getTableAlias().'.attribute_id='.$attribute->getId();
        
        if ($this->_storeId) {
            
            $condition.= ' AND '.$attribute->getTableAlias().'.store_id='.(int) $this->_storeId;
        }
        return $condition;
    }
    
    function addProductFilter($condition)
    {
        $this->_sqlSelect->where($this->_getConditionSql("$this->_productTable.product_id", $condition));
        return $this;
    }

    /**
     * Add category condotion for collection
     *
     * @param int || array $category
     */
    function addCategoryFilter($category)
    {
        if (empty($category)) {
            return $this;
        }
        
        if (!$this->_isCategoryJoined) {
            $this->_sqlSelect->join($this->_categoryProductTable, 
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
        
        foreach ($this->_attributes as $attribute) {
            if ($attribute->isSearchable()) {
                $this->_joinAttributeTable($attribute->getCode());
                $this->_sqlSelect->orWhere($attribute->getTableAlias().".attribute_value LIKE '%$query%'");
            }
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
            $field = $this->_productTable.'.'.$field;
        }
        return parent::setOrder($field, $direction);
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
    
    public function addAdminFilters($filters)
    {
        foreach ($filters as $filter) {
            switch ($filter['data']['filterType']) {
                case 'like':
                    $comparasion = 'LIKE';
                    break;
                case 'lt':
                    $comparasion = '<';
                    break;
                case 'gt':
                    $comparasion = '>';
                    break;
                case 'eq':
                    $comparasion = '=';
                    break;
                case 'neq':
                    $comparasion = '!=';
                    break;
            }
            $this->_joinAttributeTable($filter['data']['filterField'], $filter['data']['filterValue'], $comparasion);
        }
    }
    
    public function addFrontFilters(array $filters)
    {
        foreach ($filters as $filter) {
            $attribute = $filter->getAttribute();
            $value     = $filter->getValue();
            if (!empty($value)) {
                $this->_joinAttributeTable($attribute->getCode());
                $condition = $this->_conn->quoteInto($attribute->getTableAlias().'.attribute_value IN(?)', $value);
                $this->_sqlSelect->where($condition);
            }
        }
        return $this;
    }
    
    /**
     * Get uniq attribute values for current collection
     *
     * @param   int $attributeId
     * @return  array
     */
    public function getAttributeValues($attributeId)
    {
        $this->_renderFilters();
        $attribute = $this->_attributes->getItemById($attributeId);

        $select = clone $this->_sqlSelect;
        $select->reset(Zend_Db_Select::COLUMNS)
            ->group($attribute->getTableAlias().'.attribute_value');
            
        $column = $attribute->getTableAlias().'.attribute_value, count('.$attribute->getTableAlias().'.attribute_value'.') as product_count';
        
        if ($this->_isAttributeJoined($attribute)) {
            $select->from('', $column);
        }
        else {
            $select->join($attribute->getSelectTable(), $this->_getAttributeJoinCondition($attribute), $column);
        }

        return $this->_conn->fetchAll($select);
    }
    
    /**
     * Get MAX and MIN attribute values for current collection
     *
     * @param   int $attributeId
     * @return  array(
     *      'min' => 
     *      'max' =>
     *  )
     */
    public function getAttributeMinMax($attributeId)
    {
        $this->_renderFilters();
        $attribute = $this->_attributes->getItemById($attributeId);

        $select = clone $this->_sqlSelect;
        $select->reset(Zend_Db_Select::COLUMNS);
        
        $columns = 'MIN('.$attribute->getTableAlias().'.attribute_value) AS min, '. 
                   'MAX('.$attribute->getTableAlias().'.attribute_value) AS max';

        if ($this->_isAttributeJoined($attribute)){
            $select->from('', $columns);
        }
        else {
            $select->join($attribute->getSelectTable(), $this->_getAttributeJoinCondition($attribute), $columns);
        }
            
        return $this->_conn->fetchRow($select);
    }
    
    public function addTagFilter($q) {
    	$this->_sqlSelect->join($this->_tagRelTable, "{$this->_productTable}.product_id={$this->_tagRelTable}.entity_val_id AND {$this->_tagRelTable}.entity_id = 1");
    	$this->_sqlSelect->join($this->_tagTable, "{$this->_tagTable}.tag_id={$this->_tagRelTable}.tag_id");
    	$this->_sqlSelect->where("{$this->_tagTable}.tagname = '{$q}'");
    	
    	return $this;
    }
}