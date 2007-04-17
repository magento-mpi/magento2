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
    protected $_categoryProductTable;
    
    protected $_websiteId;
    protected $_isCategoryJoined=false;
    
    /**
     * All attributes collection
     *
     * @var Varin_Data_Collection_Db
     */
    protected $_attributes;
    
    protected $_joinedAttributes;
    
    function __construct($config = array())
    {
        parent::__construct(Mage::registry('resources')->getConnection('catalog_read'));
        $this->_attributes = Mage::getModel('catalog_resource', 'product_attribute_collection')->load();
        
        $this->_productTable        = Mage::registry('resources')->getTableName('catalog_resource', 'product');
        $this->_categoryProductTable= Mage::registry('resources')->getTableName('catalog_resource', 'category_product');

        $this->_sqlSelect->from($this->_productTable, new Zend_Db_Expr("SQL_CALC_FOUND_ROWS $this->_productTable.*"));
        
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('catalog_resource', 'product'));
        $this->setWebsiteId(Mage::registry('website')->getId());
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
        
        $tAlias= $attribute->getTableAlias();
        $tName = $attribute->getTableName() . ' AS ' . $tAlias;
        
        // 
        if (!isset($this->_joinedAttributes[$attribute->getCode()])) {
            $condition = "$tAlias.product_id=$this->_productTable.product_id AND $tAlias.attribute_id=".$attribute->getId();
            
            if ($this->_websiteId) {
                $condition.= " AND $tAlias.website_id=".(int) $this->_websiteId;
            }
            
            $this->_sqlSelect->join($tName, $condition, new Zend_Db_Expr("$tAlias.attribute_value AS ".$attribute->getCode()));
            $this->_joinedAttributes[$attribute->getCode()] = array();
        }
        
        //
        if ( $attributeValue !== false && (!isset($this->_joinedAttributes[$attribute->getCode()][$comparisonType]) 
             || $this->_joinedAttributes[$attribute->getCode()][$comparisonType] != $attributeValue) ) {
            
            $condition = $this->_conn->quoteInto($tAlias.".attribute_value $comparisonType ?", $attributeValue); 
            $this->_sqlSelect->where($condition);
        }
        
        return $this;
    }

    /**
     * Add category condotion for collection
     *
     * @param int || array $category
     */
    function addCategoryFilter($category)
    {
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
        /*if (!empty($query)) {
            $condition = $this->getConnection()->quoteInto("(name_varchar.attribute_value LIKE ?)", "%$query%");
            $this->addFilter('search', $condition, 'string');
        }*/
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
}