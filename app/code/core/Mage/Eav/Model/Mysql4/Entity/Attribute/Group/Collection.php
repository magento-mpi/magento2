<?php

class Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    public function _construct()
    {
        $this->_init('eav/entity_attribute_group');
    }
    
    public function setAttributeSetFilter($setId)
    {
        $this->getSelect()->where('main_table.attribute_set_id=?', $setId);
        return $this;
    }
}