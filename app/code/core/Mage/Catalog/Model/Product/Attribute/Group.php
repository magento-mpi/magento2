<?php
/**
 * Product attributes group
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Product_Attribute_Group extends Varien_Object 
{
    public function __construct($data=array()) 
    {
        parent::__construct($data);
    }
    
    public function getResource()
    {
        return Mage::getSingleton('catalog_resource', 'product_attribute_group');
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
    
    public function addAttribute(Mage_Catalog_Model_Product_Attribute $attribute, $position=null)
    {
        $this->getResource()->addAttribute($this, $attribute, $position);
        return $this;
    }

    public function removeAttribute(Mage_Catalog_Model_Product_Attribute $attribute)
    {
        $this->getResource()->removeAttribute($this, $attribute);
        return $this;
    }
    
    public function getAttributes()
    {
        $collection = Mage::getModel('catalog_resource', 'product_attribute_collection')
            //->addSetFilter($this->getSetId())
            ->addGroupFilter($this->getId())
            ->load();
        return $collection;
    }
    
    public function getAttributePosition($attribute)
    {
        $this->getResource->getAttributePosition($attribute);
    }
}