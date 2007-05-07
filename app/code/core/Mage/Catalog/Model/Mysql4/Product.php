<?php
/**
 * Product model
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Product
{
    /**
     * These made static to avoid saving in object
     *
     * @var string
     */
    protected $_productTable;
    protected $_attributeTable;
    protected $_attributeInSetTable;
    protected $_linkTable;
    
    protected $_read;
    protected $_write;

    public function __construct($data=array()) 
    {
        $this->_productTable        = Mage::registry('resources')->getTableName('catalog_resource', 'product');
        $this->_attributeTable      = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute');
        $this->_attributeInSetTable = Mage::registry('resources')->getTableName('catalog_resource', 'product_attribute_in_set');
        $this->_linkTable           = Mage::registry('resources')->getTableName('catalog_resource', 'product_link');
        
        $this->_read = Mage::registry('resources')->getConnection('catalog_read');
        $this->_write = Mage::registry('resources')->getConnection('catalog_write');
    }
    
    public function load($productId)
    {
        $productId = (int) $productId;
        $arr = array();
        
        $sql = 'SELECT * FROM ' . $this->_productTable . ' WHERE product_id=:product_id';
        $baseInfo = $this->_read->fetchRow($sql, array('product_id'=>$productId));
        if (empty($baseInfo)) {
            return $arr;
        }
        
        $select = $this->_read->select();
        $select->from($this->_productTable);
        
        $attributes = $this->getAttributes($baseInfo['set_id']);

        foreach ($attributes as $attribute) {
            // Prepare join
            $tableName = $attribute->getTableName();
            $tableAlias= $attribute->getTableAlias();
            
            $condition = "$tableAlias.product_id=".$this->_productTable.".product_id
                          AND $tableAlias.attribute_id=".$attribute->getId()."
                          AND $tableAlias.website_id=".Mage::registry('website')->getId();
            
            // Join
            if ($attribute->isRequired()) {
                $select->join($attribute->getSelectTable(), $condition, $attribute->getTableColumns());
            }
            else {
                $select->joinLeft($attribute->getSelectTable(), $condition, $attribute->getTableColumns());
            }
        }
        
        $select->where($this->_productTable.".product_id=:product_id");
        #print_r($select->__toString());
        $arr = $this->_read->fetchRow($select, array('product_id'=>$productId));

        // Add multiple attributes to result       
        $multipleAtributes = $attributes->getMultiples();
        
        foreach ($multipleAtributes as $attribute) {
            $select = $this->_read->select();
            $select->from($attribute->getSelectTable(), $attribute->getTableColumns())
                ->where('product_id='.$productId)
                ->where('attribute_id='.$attribute->getId())
                ->where('website_id='.Mage::registry('website')->getId());
            
            if ($order = $attribute->getMultipleOrder()) {
                $select->order($order);
            }
            
            if (count($attribute->getTableColumns())>1) {
                $arr[$attribute->getCode()] = $this->_read->fetchAll($select);
            }
            else {
                $arr[$attribute->getCode()] = $this->_read->fetchCol($select);
            }
        }
        
        return $arr;
    }
    
    public function save(Mage_Catalog_Model_Product $product)
    {
        $this->_write->beginTransaction();
        try {
            if (!$product->getId()) {
                $data = array(
                    'create_date'=> new Zend_Db_Expr('NOW()'),
                    'set_id'     => $product->getSetId(),
                    'type_id'    => $product->getTypeId(),
                );
                $this->_write->insert($this->_productTable, $data);
                $product->setProductId($this->_write->lastInsertId());
            }
            
            $this->_saveAttributes($product);
            $this->_saveCategories($product);
            $this->_saveLinks($product);
            
            $this->_write->commit();
        }
        catch (Exception $e){
            $this->_write->rollBack();
            throw $e;
        }
    }
    
    protected function _saveAttributes(Mage_Catalog_Model_Product $product)
    {
//        if (!$product->getData('attributes')) {
//            return false;
//        }
        $attributes = $this->getAttributes($product->getSetId());
        foreach ($attributes as $attribute) {
            if ($product->getData('attributes', $attribute->getId())) {
                $data = $product->getData('attributes', $attribute->getId());
            }
            else {
                $data = $product->getData($attribute->getCode());
            }
            
            // Check required attributes
            if ($attribute->isRequired() && empty($data)) {
                throw new Exception('Attribute "'.$attribute->getCode().'" is required');
            }
            elseif (empty($data) && !$attribute->isRequired()) {
                continue;
            }
            
            $saver = $attribute->getSaver()->save($product->getId(), $data);
            $product->setData($attribute->getCode(), $data);
        }
        return $this;
    }
    
    protected function _saveCategories(Mage_Catalog_Model_Product $product)
    {
        
    }
    
    protected function _saveLinks(Mage_Catalog_Model_Product $product)
    {
        if (($related = $product->getRelatedLinks()) && is_array($related)) {
            $linkType = 1; // TODO: get from config or model constant
            $condition = $this->_write->quoteInto('product_id=?', $product->getId())
                       . ' AND ' . $this->_write->quoteInto('link_type_id=?', $linkType);
            $this->_write->delete($this->_linkTable, $condition);
            $data = array(
                'product_id'    => $product->getId(),
                'link_type_id'  => $linkType,
            );
            
            foreach ($related as $productId) {
                $data['linked_product_id'] = $productId;
                $this->_write->insert($this->_linkTable, $data);
            }
        }
    }
    
    protected function _prepareSaveData(Mage_Catalog_Model_Product $product)
    {
        $data = array();
    }
    
    /**
     * Get product attributes collection
     *
     * @param   int $productId
     * @return  array
     */
    public function getAttributes($attributeSetId)
    {
        $collection = Mage::getModel('catalog_resource', 'product_attribute_collection')
            ->addSetFilter($attributeSetId)
            ->load();
        return $collection;
    }
}