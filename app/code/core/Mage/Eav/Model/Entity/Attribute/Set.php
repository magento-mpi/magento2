<?php

class Mage_Eav_Model_Entity_Attribute_Set extends Mage_Core_Model_Abstract 
{
    protected function _construct()
    {
        $this->setResourceModel('eav/entity_attribute_set');
        $this->setIdFieldName('entity_type_id');
        parent::_construct();
    }
}