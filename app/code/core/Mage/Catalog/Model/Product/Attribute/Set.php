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
        return Mage::getSingleton('catalog_resource', 'product_attribute_set');
    }

    public function load($setId)
    {
        $this->setData($this->getResource()->load($setId));
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
        $collection = Mage::getModel('catalog_resource', 'product_attribute_collection')
            ->addSetFilter($this->getId())
            ->setPositionOrder()
            ->loadData();
        return $collection;
    }
    
    public function moveAttribute($attribute, $fromGroup, $toGroup, $position=null)
    {
        if (is_numeric($attribute)) {
            $attribute = Mage::getModel('catalog', 'product_attribute')->load($attribute);
        }
        
        if (!$attribute instanceof Mage_Catalog_Model_Product_Attribute || !$attribute->getAttributeId()) {
            return $this;
        }
        
        if (is_numeric($fromGroup)) {
            $fromGroup = Mage::getModel('catalog', 'product_attribute_group')->load($fromGroup);
        }
        if (!$fromGroup instanceof Mage_Catalog_Model_Product_Attribute_Group || !$fromGroup->getGroupId()) {
            return $this;
        }        
        if (is_numeric($toGroup)) {
            $toGroup = Mage::getModel('catalog', 'product_attribute_group')->load($toGroup);
        }
        if (!$toGroup instanceof Mage_Catalog_Model_Product_Attribute_Group || !$toGroup->getGroupId()) {
            return $this;
        }
        
        $fromGroup->removeAttribute($attribute);
        $toGroup->addAttribute($attribute, $position);

        return $this;
    }
}