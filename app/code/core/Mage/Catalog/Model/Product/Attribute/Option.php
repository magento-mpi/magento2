<?php
/**
 * Product attribute option
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Product_Attribute_Option extends Varien_Object 
{
    public function __construct($data=array()) 
    {
        parent::__construct($data);
    }
    
    public function getResource()
    {
        return Mage::getSingleton('catalog_resource', 'product_attribute_option');
    }
    
    public function load($optionId)
    {
        $this->setData($this->getResource()->load($optionId));
        return $this;
    }
    
    public function getId()
    {
        return $this->getOptionId();
    }
}