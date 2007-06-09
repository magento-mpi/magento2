<?php
/**
 * Product attribute default saver
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Product_Attribute_Saver
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
        return Mage::getSingleton('catalog_resource/product_attribute_saver');
    }
    
    public function save($productId, $value)
    {
        $this->getResource()->save($this->_attribute, $productId, $value);
        return $this;
    }
}