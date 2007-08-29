<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Category model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
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
        $this->_categoryTable   = Mage::getSingleton('core/resource')->getTableName('catalog/category');
        $this->_attributeTable  = Mage::getSingleton('core/resource')->getTableName('catalog/category_attribute');
        $this->_attributeValueTable  = Mage::getSingleton('core/resource')->getTableName('catalog/category_attribute_value');
        
        $this->_read = Mage::getSingleton('core/resource')->getConnection('catalog_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('catalog_write');
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
                              AND $tableAlias.store_id=".Mage::getSingleton('core/store')->getId();
                
                $select->joinLeft($attribute->getSelectTable(), $condition, $attribute->getTableColumns());
            }
            $select->where($this->_categoryTable . ".category_id=?", $categoryId);
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
                
                $tree = Mage::getResourceModel('catalog/category_tree');
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
        $collection = Mage::getResourceModel('catalog/category_attribute_collection')
            ->addSetFilter($setId)
            ->load();
        return $collection;
    }
}