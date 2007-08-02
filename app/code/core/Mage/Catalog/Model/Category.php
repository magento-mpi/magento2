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
    /**
     * Category display modes
     */
    const DM_PRODUCT= 'PRODUCTS';
    const DM_PAGE   = 'PAGE';
    const DM_MIXED  = 'PRODUCTS_AND_PAGE';
    
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
    
    /**
     * Retrieve category tree model
     *
     * @return unknown
     */
    public function getTreeModel()
    {
        return Mage::getResourceModel('catalog/category_tree');
    }
    
    /**
     * Set category and resource model store id
     *
     * @param unknown_type $storeId
     * @return unknown
     */
    public function setStoreId($storeId)
    {
        $this->getResource()->setStore($storeId);
        $this->setData('store_id', $storeId);
        return $this;
    }
    
    /**
     * Retrieve category store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getResource()->getStoreId();
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
            ->getAttributesByCode();
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
    
    /**
     * Retrieve array of store ids for category
     *
     * @return array
     */
    public function getStoreIds()
    {
        if ($storeIds = $this->getData('store_ids')) {
            return $storeIds;
        }
        $storeIds = $this->getResource()->getStoreIds($this);
        $this->setData('store_ids', $storeIds);
        return $storeIds;
    }
    
    
    public function getLayoutUpdateFileName()
    {
        switch ($this->getDisplayMode()) {
            case self::DM_PAGE:
                $layout = 'catalog/category/content.xml';
                break;
            case self::DM_MIXED:
                $layout = 'catalog/category/mixed.xml';
                break;
            default:
                $layout = 'catalog/category/default.xml';
                break;
        }
        return $layout;
    }
}
