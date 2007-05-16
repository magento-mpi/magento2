<?php
/**
 * Category attribute saver model
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Category_Attribute_Saver 
{
    /**
     * Attribute object
     *
     * @var Mage_Catalog_Model_Product_Attribute
     */
    protected $_attribute;
    
    public function __construct() 
    {
        
    }
    
    public function setAttribute($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    public function getResource()
    {
        return Mage::getSingleton('catalog_resource', 'category_attribute_saver');
    }
    
    public function save($categoryId, $value)
    {
        $this->getResource()->save($this->_attribute, $categoryId, $value);
        return $this;
    }
}