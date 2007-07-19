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
     * Get category filters
     *
     * @return Varien_Data_Collection_Db
     */
    public function getFilters()
    {
        $collection = Mage::getResourceModel('catalog/category_filter_collection')
            ->addCategoryFilter($this->getId())
            ->load();
        return $collection;
    }
    
    /**
     * Get stores collection for category
     *
     * @return Varien_Data_Collection_Db
     */
    public function getStores()
    {
        $arrNodes = Mage::getResourceModel('catalog/category_tree')
            ->load()
            ->getPath($this->getId());
        $arrCategoryId = array();
        
        foreach ($arrNodes as $node) {
            $arrCategoryId[] = $node->getId();
        }
        
        $collection = Mage::getResourceModel('core/store_collection')
            ->addCategoryFilter($arrCategoryId)
            ->load();
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

}