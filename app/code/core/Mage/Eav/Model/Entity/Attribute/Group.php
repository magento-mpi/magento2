<?php

class Mage_Eav_Model_Entity_Attribute_Group extends Mage_Core_Model_Abstract 
{
    protected function _construct()
    {
        $this->_setResource('eav/entity_attribute_group'); 
        parent::_construct();
    }
    
    public function getId()
    {
        return $this->getData('attribute_group_id');
    }
    
    public function setId($id)
    {
        return $this->setData('attribute_group_id', $id);
    }
}