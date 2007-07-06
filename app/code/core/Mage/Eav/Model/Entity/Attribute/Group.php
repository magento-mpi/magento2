<?php

class Mage_Eav_Model_Entity_Attribute_Group extends Mage_Core_Model_Abstract 
{
    protected function _construct()
    {
        $this->_setResource('eav/entity_attribute_group'); 
        $this->setIdFieldName('entity_type_id');
        parent::_construct();
    }
}