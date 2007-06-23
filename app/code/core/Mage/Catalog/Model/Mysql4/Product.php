<?php
/**
 * Product model
 *
 * @package    Mage
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
    protected $_categoryProductTable;
    protected $_attributeTable;
    protected $_attributeInSetTable;
    protected $_linkTable;
    
    protected $_attributes;
    
    protected $_read;
    protected $_write;

    public function __construct($data=array()) 
    {
        $this->_productTable        = Mage::getSingleton('core/resource')->getTableName('catalog/product');
        $this->_attributeTable      = Mage::getSingleton('core/resource')->getTableName('catalog/product_attribute');
        $this->_attributeInSetTable = Mage::getSingleton('core/resource')->getTableName('catalog/product_attribute_in_set');
        $this->_linkTable           = Mage::getSingleton('core/resource')->getTableName('catalog/product_link');
        $this->_categoryProductTable= Mage::getSingleton('core/resource')->getTableName('catalog/category_product');
        
        $this->_read = Mage::getSingleton('core/resource')->getConnection('catalog_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('catalog_write');
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
                          AND $tableAlias.store_id=".Mage::getSingleton('core/store')->getId();
            
            // Join
            if ($attribute->isRequired()) {
                $select->join($attribute->getSelectTable(), $condition, $attribute->getTableColumns());
            }
            else {
                $select->joinLeft($attribute->getSelectTable(), $condition, $attribute->getTableColumns());
            }
        }
        
        $select->where($this->_productTable.".product_id=:product_id");
        $arr = $this->_read->fetchRow($select, array('product_id'=>$productId));

        // Add multiple attributes to result       
        $multipleAtributes = $attributes->getMultiples();
        
        foreach ($multipleAtributes as $attribute) {
            $select = $this->_read->select();
            $select->from($attribute->getSelectTable(), $attribute->getTableColumns())
                ->where('product_id='.$productId)
                ->where('attribute_id='.$attribute->getId())
                ->where('store_id='.Mage::getSingleton('core/store')->getId());
            
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
                $this->_checkRequiredAttributes($product);
                $data = array(
                    'create_date'=> new Zend_Db_Expr('NOW()'),
                    'set_id'     => $product->getSetId(),
                    'type_id'    => $product->getTypeId(),
                );
                
                $this->_write->insert($this->_productTable, $data);
                $product->setProductId($this->_write->lastInsertId());
            }
            else {
                $this->_checkRequiredAttributes($product, true);
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
    
    protected function _checkRequiredAttributes($product, $onlyExistingAttributes = false)
    {
        $values = $product->getData('attributes');
        foreach ($this->getAttributes($product->getSetId()) as $attribute) {
        	if ($attribute->isRequired()) {
        	    if ($onlyExistingAttributes) {
        	        if (isset($values[$attribute->getId()]) && empty($values[$attribute->getId()])) {
        	            throw new Exception('Attribute "'.__($attribute->getCode()).'" is required');
        	        }
        	    }
        	    else {
        	        if (empty($values[$attribute->getId()])) {
        	            throw new Exception('Attribute "'.__($attribute->getCode()).'" is required');
        	        }
        	    }
        	}
        }
        return $this;
    }
    
    protected function _saveAttributes(Mage_Catalog_Model_Product $product)
    {
        $attributes = $this->getAttributes($product->getSetId());
        $values = $product->getData('attributes');
        if (empty($values)) {
            return $this;
        }
        
        // save attributes values
        foreach ($values as $attributeId=>$attributeValue) {
            $attribute = $attributes->getItemById($attributeId);
            if ($attribute instanceof Mage_Catalog_Model_Product_Attribute) {
                $saver = $attribute->getSaver()->save($product->getId(), $attributeValue);
                $product->setData($attribute->getCode(), $attributeValue);
            }
            else {
                throw new Exception('Attribute with id "'.$attributeId.'" not exists');
            }
        }
        return $this;
    }
    
    protected function _saveCategories(Mage_Catalog_Model_Product $product)
    {
        if (($categories=$product->getNewCategories()) && is_array($categories)) {
            $condition = $this->_write->quoteInto('product_id=?', $product->getId());
            $this->_write->delete($this->_categoryProductTable, $condition);
            
            $data = array('product_id'=>$product->getId());
            foreach ($categories as $categoryId) {
                if ($categoryId = (int) $categoryId) {
                	$data['category_id'] = $categoryId;
                	$this->_write->insert($this->_categoryProductTable, $data);
                }
            }
        }
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
                if ($productId = (int) $productId) {
                    $data['linked_product_id'] = $productId;
                    $this->_write->insert($this->_linkTable, $data);
                }
            }
        }
    }
    
    /**
     * Get product attributes collection
     *
     * @param   int $productId
     * @return  array
     */
    public function getAttributes($attributeSetId)
    {
        if (!$this->_attributes) {
            $this->_attributes = Mage::getResourceModel('catalog/product_attribute_collection')
                ->addSetFilter($attributeSetId)
                ->load();
        }
        return $this->_attributes;
    }
}