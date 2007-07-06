<?php

class Mage_Eav_Model_Mysql4_Entity_Attribute_Set_Collection extends Mage_Core_Model_Resource_Collection_Abstract 
{
    public function _construct()
    {
        $this->setModel('eav/attribute_set');
    }
    
    public function setEntityTypeFilter($typeId)
    {
        $this->getSelect()->where('entity_type_id=?', $typeId);
        return $this;
    }
}