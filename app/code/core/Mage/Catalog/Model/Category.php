<?php
/**
 * Catalog category
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
abstract class Mage_Catalog_Model_Category extends Varien_Data_Object
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
     * Load category data
     *
     * @param   int $categoryId
     * @return  Mage_Catalog_Model_Category
     */
    public function load($categoryId)
    {
        $this->setCategoryId($categoryId);
        return $this;
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
    
    public function getName()
    {
        
    }

    /**
     * Get category products collection
     *
     * @return Varien_Data_Collection_Db
     */
    public function getProductCollection()
    {
        $collection = Mage::getModel('catalog', 'product_collection');
        $collection->addCategoryFilter($this->getCategoryId());
        return $collection;
    }
    
    
    public function getAttributes()
    {
        
    }
}