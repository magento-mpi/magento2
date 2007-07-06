<?php

class Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection extends Mage_Core_Model_Resource_Collection_Abstract 
{
    public function _construct()
    {
        $this->setModel('eav/attribute_group');
    }
    
    public function setAttributeSetFilter($setId)
    {
        $this->getSelect()->where('attribute_set_id=?', $setId);
        return $this;
    }
}