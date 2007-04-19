<?php
/**
 * Catalog category
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Category extends Varien_Data_Object
{
    public function __construct($category=false) 
    {
        parent::__construct();
        
        if (is_numeric($category)) {
            $this->load($category);
        }
        elseif (is_array($category)) {
            $this->setData($category);
        }
    }

    /**
     * Get category id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getCategoryId();
    }
    
    public function getResource()
    {
        static $resource;
        if (!$resource) {
            $resource = Mage::getModel('catalog_resource', 'category');
        }
        return $resource;
    }

    /**
     * Load category data
     *
     * @param   int $categoryId
     * @return  Mage_Catalog_Model_Category
     */
    public function load($categoryId)
    {
        $this->setData($this->getResource()->load($categoryId));
        return $this;
    }
    
    /**
     * Get category products collection
     *
     * @return Varien_Data_Collection_Db
     */
    public function getProductCollection()
    {
        $collection = Mage::getModel('catalog_resource', 'product_collection')
            ->addCategoryFilter($this->getId());
        return $collection;
    }
    
    public function getFilters()
    {
        $collection = Mage::getModel('catalog_resource', 'category_filter_collection')
            ->addCategoryFilter($this->getId())
            ->load();
        return $collection;
    }
}