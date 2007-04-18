<?php
/**
 * Product attribute option
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Product_Attribute_Option extends Varien_Data_Object 
{
    public function __construct($data=array()) 
    {
        parent::__construct($data);
    }
    
    public function getResource()
    {
        static $resource;
        if (!$resource) {
            $resource = Mage::getModel('catalog_resource', 'product_attribute_option');
        }
        return $resource;
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