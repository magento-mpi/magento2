<?php
/**
 * Product collection
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Entity_Product_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    protected $_productStoreTable;
    protected $_categoryProductTable;
    protected $_storeTable;
    
    public function __construct() 
    {
        $this->setEntity(Mage::getResourceSingleton('catalog/product'));
        $this->setObject('catalog/product');
        
        $resource = Mage::getSingleton('core/resource');
        $this->_productStoreTable = $resource->getTableName('catalog/product_store');
        $this->_storeTable        = $resource->getTableName('core/store');
        $this->_categoryProductTable = $resource->getTableName('catalog/category_product');
    }
    
    public function addIdFilter($productId)
    {
        if (is_array($productId)) {
            $condition = array('in'=>$productId);
        }
        else {
            $condition = $productId;
        }
        $this->addFieldToFilter('entity_id', $condition);
        return $this;
    }
    
    public function addCategoryFilter(Mage_Catalog_Model_Category $category, $renderAlias=false)
    {
        if ($category->getIsAnchor()) {
            $categoryCondition = $this->_read->quoteInto('{{table}}.category_id IN (?)', explode(',', $category->getAllChildren()));
            $this->getSelect()->distinct(true);
        }
        else {
            $categoryCondition = $this->_read->quoteInto('{{table}}.category_id=?', $category->getId());
        }
        if ($renderAlias) {
            $alias = 'category_'.$category->getId();
        }
        else {
            $alias = 'position';
        }
        
        $this->joinField($alias, 
                'catalog/category_product', 
                'position', 
                'product_id=entity_id', 
                $categoryCondition);
                
        return $this;
    }
    
    /**
     * Adding product store names to result collection
     *
     * @return Mage_Catalog_Model_Entity_Product_Collection
     */
    public function addStoreNamesToResult()
    {
        $productStores = array();
        foreach ($this as $product) {
        	$productStores[$product->getId()] = array();
        }
        
        if (!empty($productStores)) {
            $select = $this->_read->select()
                ->from($this->_productStoreTable)
                ->join($this->_storeTable, $this->_storeTable.'.store_id='.$this->_productStoreTable.'.store_id')
                ->where($this->_read->quoteInto($this->_productStoreTable.'.product_id IN (?)', array_keys($productStores)))
                ->where($this->_storeTable.'.store_id>0');

            $data = $this->_read->fetchAll($select);
            foreach ($data as $row) {
            	$productStores[$row['product_id']][] = $row['name'];
            }
        }
        
        foreach ($this as $product) {
            if (isset($productStores[$product->getId()])) {
                $product->setData('stores', $productStores[$product->getId()]);
            }
        }
        return $this;
    }
    
    /**
     * Retrieve max value by attribute
     *
     * @param   string $attribute
     * @return  mixed
     */
    public function getMaxAttributeValue($attribute)
    {
        $select     = clone $this->getSelect();
        $attribute  = $this->getEntity()->getAttribute($attribute);
        $attributeCode = $attribute->getAttributeCode();
        $tableAlias = $attributeCode.'_max_value';
        
        $condition  = 'e.entity_id='.$tableAlias.'.entity_id 
            AND '.$this->_getConditionSql($tableAlias.'.attribute_id', $attribute->getId()).'
            AND '.$this->_getConditionSql($tableAlias.'.store_id', $this->getEntity()->getStoreId());
        
        $select->join(
                array($tableAlias => $attribute->getBackend()->getTable()),
                $condition,
                array('max_'.$attributeCode=>new Zend_Db_Expr('MAX('.$tableAlias.'.value)'))
            )
            ->group('e.entity_type_id');
            
        $data = $this->_read->fetchRow($select);
        if (isset($data['max_'.$attributeCode])) {
            return $data['max_'.$attributeCode];
        }
        return null;
    }
    
    /**
     * Retrieve ranging product count for arrtibute range
     *
     * @param   string $attribute
     * @param   int $range
     * @return  array
     */
    public function getAttributeValueCountByRange($attribute, $range)
    {
        $select     = clone $this->getSelect();
        $attribute  = $this->getEntity()->getAttribute($attribute);
        $attributeCode = $attribute->getAttributeCode();
        $tableAlias = $attributeCode.'_range_count_value';
        
        $condition  = 'e.entity_id='.$tableAlias.'.entity_id 
            AND '.$this->_getConditionSql($tableAlias.'.attribute_id', $attribute->getId()).'
            AND '.$this->_getConditionSql($tableAlias.'.store_id', $this->getEntity()->getStoreId());
        
        $select->join(
                array($tableAlias => $attribute->getBackend()->getTable()),
                $condition,
                array(
                        'count_'.$attributeCode=>new Zend_Db_Expr('COUNT(DISTINCT e.entity_id)'),
                        'range_'.$attributeCode=>new Zend_Db_Expr('CEIL('.$tableAlias.'.value/'.$range.')')
                     )
            )
            ->group('range_'.$attributeCode);

        $data   = $this->_read->fetchAll($select);
        $res    = array();
        
        foreach ($data as $row) {
        	$res[$row['range_'.$attributeCode]] = $row['count_'.$attributeCode];
        }
        return $res;
    }
    
    /**
     * Retrieve product count by some value of attribute
     *
     * @param   string $attribute
     * @return  array($value=>$count)
     */
    public function getAttributeValueCount($attribute)
    {
        $select     = clone $this->getSelect();
        $attribute  = $this->getEntity()->getAttribute($attribute);
        $attributeCode = $attribute->getAttributeCode();
        $tableAlias = $attributeCode.'_value_count';
        
        $condition  = 'e.entity_id='.$tableAlias.'.entity_id 
            AND '.$this->_getConditionSql($tableAlias.'.attribute_id', $attribute->getId()).'
            AND '.$this->_getConditionSql($tableAlias.'.store_id', $this->getEntity()->getStoreId());
        
        $select->join(
                array($tableAlias => $attribute->getBackend()->getTable()),
                $condition,
                array(
                        'count_'.$attributeCode=>new Zend_Db_Expr('COUNT(DISTINCT e.entity_id)'),
                        'value_'.$attributeCode=>new Zend_Db_Expr($tableAlias.'.value')
                     )
            )
            ->group('value_'.$attributeCode);

        $data   = $this->_read->fetchAll($select);
        $res    = array();
        
        foreach ($data as $row) {
        	$res[$row['value_'.$attributeCode]] = $row['count_'.$attributeCode];
        }
        return $res;
    }
    
    /**
     * Render SQL for retrieve product count
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        $sql = $countSelect->__toString();
        $sql = preg_replace('/^select\s+.+?\s+from\s+/is', 'select count(DISTINCT e.entity_id) from ', $sql);
        return $sql;
    }
    
    /**
     * Adding product count to categories collection
     *
     * @param   Mage_Eav_Model_Entity_Collection_Abstract $categoryCollection
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addCountToCategories($categoryCollection)
    {
        foreach ($categoryCollection as $category) {
        	$select     = clone $this->getSelect();
        	$select->reset(Zend_Db_Select::COLUMNS);
        	$select->distinct(false);
            $select->join(
                    array('category_count_table' => $this->_categoryProductTable),
                    'category_count_table.product_id=e.entity_id',
                    array('count_in_category'=>new Zend_Db_Expr('COUNT(DISTINCT e.entity_id)'))
                );
                
            if ($category->getIsAnchor()) {
                $select->where($this->_read->quoteInto('category_count_table.category_id IN(?)', explode(',', $category->getAllChildren())));
            }
            else {
                $select->where($this->_read->quoteInto('category_count_table.category_id=?', $category->getId()));
            }

        	$category->setProductCount((int) $this->_read->fetchOne($select));
        }
        return $this;
    }
    
    public function addSearchFilter($query)
    {
        $entity = $this->getEntity();
        
        $attributesCollection = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($this->getEntity()->getConfig()->getId())
            ->load();
        
        $tables = array();
        foreach ($attributesCollection as $attribute) {
            $attribute->setEntity($entity);
        	if ($attribute->getIsSearchable()) {
        	    $table = $attribute->getBackend()->getTable();
        	    if (!isset($tables[$table])) {
        	        $tables[$table] = array();
        	        $tables[$table]['attributes']  = array();
        	        $tables[$table]['condition']   = array();
        	    }
        	    $tables[$table]['attributes'][] = $attribute->getId();
        	}
        }
        
        $select = $this->getSelect()
            ->distinct(true);
        $condition = array();
        
        foreach ($tables as $tableName => $info) {
            $tableAlias = $tableName . '_for_search';
            $valueAlias = $tableName . '_value';
            
            $joinCondition = $tableAlias.'.entity_id=e.entity_id AND ' . 
                $this->_read->quoteInto($tableAlias.'.store_id =? AND ', $entity->getStoreId()) .
                $this->_read->quoteInto($tableAlias.'.attribute_id IN (?)', $info['attributes']);
        	$select->joinLeft(array($tableAlias=>$tableName), $joinCondition, new Zend_Db_Expr('1'));
        	$condition[] = $tableAlias . '.value LIKE \'%' . $query . '%\'';
        }
        
        $condition = '(' . implode(') OR (', $condition) . ')';
        $select->where($condition);
        /*$filters = array();
        foreach ($attributesCollection as $attribute) {
        	if ($attribute->getIsSearchable()) {
        	    $filters[] = array(
        	       'attribute' => $attribute->getAttributeCode(),
        	       'like'      => $query.'%',
        	    );
        	}
        }
        
        
    	$this->addAttributeToFilter($filters);*/
        
    	return $this;
    }
}
