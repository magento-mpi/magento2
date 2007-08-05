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
    protected $_storeTable;
    
    public function __construct() 
    {
        $this->setEntity(Mage::getResourceSingleton('catalog/product'));
        $this->setObject('catalog/product');
        
        $this->_productStoreTable = Mage::getSingleton('core/resource')->getTableName('catalog/product_store');
        $this->_storeTable        = Mage::getSingleton('core/resource')->getTableName('core/store');
    }
    
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
                        'count_'.$attributeCode=>new Zend_Db_Expr('COUNT('.$tableAlias.'.value)'),
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

    public function getAttributeValueCount($attribute)
    {
        
    }
}
