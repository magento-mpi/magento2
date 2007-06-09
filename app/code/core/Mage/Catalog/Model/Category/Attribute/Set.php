<?php
/**
 * Catalog category attribute set
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Category_Attribute_Set extends Varien_Object  
{
    public function __construct($data=array()) 
    {
        parent::__construct($data);
    }
    
    public function getResource()
    {
        return Mage::getSingleton('catalog_resource/category_attribute_set');
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
        return $this->getAttributeSetId();
    }
    
    public function getAttributes()
    {
        $collection = Mage::getModel('catalog_resource/category_attribute_collection')
            ->addSetFilter($this->getId())
            ->setPositionOrder()
            ->load();
        return $collection;
    }
}