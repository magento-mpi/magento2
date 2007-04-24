<?php
/**
 * Product attributes group
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Product_Attribute_Group extends Varien_Data_Object 
{
    public function __construct($data=array()) 
    {
        parent::__construct($data);
    }
    
    public function getResource()
    {
        static $resource;
        if (!$resource) {
            $resource = Mage::getModel('catalog_resource', 'product_attribute_group');
        }
        return $resource;
    }
    
    public function getId()
    {
        return $this->getGroupId();
    }
    
    public function load($groupId)
    {
        $this->setData($this->getResource()->load($groupId));
        return $this;
    }
    
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }
    
    public function delete()
    {
        $this->getResource()->delete($this->getId());
        return $this;
    }
    
    /**
     * Change group position
     *
     * @param  Mage_Catalog_Model_Product_Attribute_Group  $prevGroup
     * @return this
     */
    public function moveAfter(Mage_Catalog_Model_Product_Attribute_Group $prevGroup)
    {
        $this->getResource()->moveAfter($prevGroup);
        return $this;
    }
    
    public function getAttributesBySet($setId)
    {
        $collection = Mage::getModel('catalog_resource', 'product_attribute_collection')
            ->addSetFilter($setId)
            ->addGroupFilter($this->getId())
            ->load();
        return $collection;
    }
}