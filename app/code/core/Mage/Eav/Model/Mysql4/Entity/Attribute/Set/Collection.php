<?php

class Mage_Eav_Model_Mysql4_Entity_Attribute_Set_Collection extends Mage_Core_Model_Resource_Collection_Abstract 
{
    public function _construct()
    {
        $this->_init('eav/entity_attribute_set');
    }
    
    public function setEntityTypeFilter($typeId)
    {
        $this->getSelect()->where('main_table.entity_type_id=?', $typeId);
        return $this;
    }
}