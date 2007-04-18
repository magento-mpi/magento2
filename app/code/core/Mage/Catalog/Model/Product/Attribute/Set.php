<?php
/**
 * Product attributes set
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Product_Attribute_Set extends Varien_Data_Object 
{
    public function __construct($data=array()) 
    {
        parent::__construct($data);
    }
    
    public function getResource()
    {
        static $resource;
        if (!$resource) {
            $resource = Mage::getModel('catalog_resource', 'product_attribute_set');
        }
        return $resource;
    }

    public function load($setId)
    {
        $this->setData($this->getResource()->load($setId));
        return $this;
    }
    
    public function getId()
    {
        return $this->getSetId();
    }
    
    public function getGroups()
    {
        $collection = Mage::getModel('catalog_resource', 'product_attribute_group_collection')
            ->addSetFilter($this->getId())
            ->load();
        return $collection;
    }
    
    public function getAttributes()
    {
        
    }
}