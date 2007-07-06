<?php

class Mage_Eav_Model_Entity_Type extends Mage_Core_Model_Abstract
{
    protected $_attributes;
    protected $_sets;
    
    protected function _construct()
    {
        $this->_setResource('eav/entity_type'); 
        parent::_construct();
    }
    
    public function getId()
    {
        return $this->getData('entity_type_id');
    }
    
    public function setId($id)
    {
        return $this->setData('entity_type_id', $id);
    }
    
    public function getAttributeCollection()
    {
        if (empty($this->_attributes)) {
            $this->_attributes = Mage::getModel('eav/attribute')->getResourceCollection()
                ->setEntityTypeFilter($this->getId())
                ->load();
        }
        return $this->_attributes;
    }
    
    public function getAttributeSetCollection()
    {
        if (empty($this->_sets)) {
            $this->_sets = Mage::getModel('eav/attribute_set')->getResourceCollection()
                ->setEntityTypeFilter($this->getId())
                ->load();
        }
        return $this->_sets;
    }
}