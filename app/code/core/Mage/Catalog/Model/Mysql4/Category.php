<?php

/**
 * Category model
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Mysql4_Category
{
    protected $_categoryTable;
    protected $_attributeTable;
    protected $_attributeValueTable;
    protected $_read;
    protected $_write;
    
    public function __construct() 
    {
        $this->_categoryTable   = Mage::registry('resources')->getTableName('catalog_resource', 'category');
        $this->_attributeTable  = Mage::registry('resources')->getTableName('catalog_resource', 'category_attribute');
        $this->_attributeValueTable  = Mage::registry('resources')->getTableName('catalog_resource', 'category_attribute_value');
        
        $this->_read = Mage::registry('resources')->getConnection('catalog_read');
        $this->_write = Mage::registry('resources')->getConnection('catalog_write');
    }
    
    /**
     * Load category
     *
     * @param   int $categoryId
     * @return  array
     */
    public function load($categoryId)
    {
        $arr = array();
        $sql = "SELECT * FROM $this->_categoryTable WHERE category_id=:category_id";
        
        $categoryRow = $this->_read->fetchRow($sql, array('category_id'=>$categoryId));
        
        if (empty($categoryRow)) {
            return $arr;
        }
        
        $attributes = $this->getAttributesBySet($categoryRow['attribute_set_id']);
        if ($attributes->getSize()) {
            
            $select = $this->_read->select();
            $select->from($this->_categoryTable);

            foreach ($attributes as $index => $attribute) {
                // Prepare join
                $tableAlias= $attribute->getTableAlias();
                $condition = "$tableAlias.category_id=".$this->_categoryTable.".category_id
                              AND $tableAlias.attribute_id=".$attribute->getId()."
                              AND $tableAlias.website_id=".Mage::registry('website')->getId();
                
                $select->joinLeft($attribute->getSelectTable(), $condition, $attribute->getTableColumns());
            }
            $select->where($this->_categoryTable . ".category_id=$categoryId");
            $arr = $this->_read->fetchRow($select);
        }
        return $arr;
    }
    
    public function save(Mage_Catalog_Model_Category $category)
    {
        $this->_write->beginTransaction();
        try {
            if (!$category->getId()) {
                $parentId = $category->getParentId();
                if (!$parentId) {
                    throw new Exception('Empty parent id for category');
                }
                
                $tree = Mage::getModel('catalog_resource', 'category_tree');
                $parentNode = $tree->loadNode($parentId);
                
                $node = $tree->appendChild(array('attribute_set_id'=>$category->getAttributeSetId()), $parentNode);
                $category->setCategoryId($node->getId());
            }

            $this->_saveAttributes($category);
            $this->_write->commit();
        }
        catch (Exception $e){
            $this->_write->rollBack();
            throw $e;
        }
    }
    
    protected function _saveAttributes(Mage_Catalog_Model_Category $category)
    {
        $attributes = $this->getAttributesBySet($category->getAttributeSetId());
        foreach ($attributes as $attribute) {
            if ($category->getData('attributes', $attribute->getId())) {
                $data = $category->getData('attributes', $attribute->getId());
            }
            else {
                $data = $category->getData($attribute->getCode());
            }
            
            
            // Check required attributes
            if ($attribute->isRequired() && empty($data)) {
                throw new Exception('Attribute "'.$attribute->getCode().'" is required');
            }
            
            $saver = $attribute->getSaver()->save($category->getId(), $data);
            $category->setData($attribute->getCode(), $data);
        }
        return $this;
    }
    
    /**
     * Get category attributes
     *
     * @return unknown
     */
    public function getAttributesBySet($setId)
    {
        $collection = Mage::getModel('catalog_resource', 'category_attribute_collection')
            ->addSetFilter($setId)
            ->load();
        return $collection;
    }
}