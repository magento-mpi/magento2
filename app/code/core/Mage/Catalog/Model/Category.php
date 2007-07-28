<?php
/**
 * Catalog category
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Category extends Varien_Object
{
    public function __construct() 
    {
        parent::__construct();
        $this->setIdFieldName($this->getResource()->getEntityIdField());
    }
    
    /**
     * Retrieve category resource model
     *
     * @return Mage_Eav_Model_Entity_Abstract
     */
    public function getResource()
    {
        return Mage::getResourceSingleton('catalog/category');
    }
    
    public function getTreeModel()
    {
        return Mage::getResourceModel('catalog/category_tree');
    }
    
    public function setStoreId($storeId)
    {
        $this->getResource()->setStore($storeId);
        $this->setData('store_id', $storeId);
        return $this;
    }

    /**
     * Load category data
     *
     * @param   int $categoryId
     * @return  Mage_Catalog_Model_Category
     */
    public function load($categoryId)
    {
        $this->getResource()->load($this, $categoryId);
        return $this;
    }
    
    /**
     * Save category
     *
     * @return Mage_Catalog_Model_Category
     */
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }
    
    /**
     * Delete category
     *
     * @return Mage_Catalog_Model_Category
     */
    public function delete()
    {
        $this->getResource()->delete($this);
        return $this;
    }
    
    /**
     * Get category products collection
     *
     * @return Varien_Data_Collection_Db
     */
    public function getProductCollection()
    {
        $collection = Mage::getResourceModel('catalog/product_collection');
            //->addCategoryFilter($this->getId());
        return $collection;
    }
    
    /**
     * Retrieve all customer attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->getResource()
            ->loadAllAttributes()
            ->getAttributesByName();
    }
    
    /**
     * Retrieve array of product id's for category
     * 
     * array($productId => $position)
     * 
     * @return array
     */
    public function getProductsPosition()
    {
        if (!$this->getId()) {
            return array();
        }
        
        $arr = $this->getData('products_position');
        if (is_null($arr)) {
            $arr = $this->getResource()->getProductsPosition($this);
            $this->setData('products_position', $arr);
        }
        return $arr;
    }
}