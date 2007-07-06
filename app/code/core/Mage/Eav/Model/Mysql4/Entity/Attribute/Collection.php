<?php

class Mage_Eav_Model_Mysql4_Entity_Attribute_Collection extends Mage_Core_Model_Resource_Collection_Abstract 
{
    public function _construct()
    {
        $this->setModel('eav/entity_attribute');
    }
    
    public function setEntityTypeFilter($typeId)
    {
        $this->getSelect()->where('main_table.entity_type_id=?', $typeId);
        return $this;
    }
    
    public function setAttributeSetFilter($setId)
    {
        $this->join('entity_attribute', 'entity_attribute.attribute_id=main_table.attribute_id', 'sort_order');
        $this->getSelect()->where('entity_attribute.attribute_set_id=?', $setId);
        $this->setOrder('sort_order', 'asc');
        return $this;
    }
    
    
    public function setAttributeGroupFilter($groupId)
    {
        $this->join('entity_attribute', 'entity_attribute.attribute_id=main_table.attribute_id', 'sort_order');
        $this->getSelect()->where('main_table.attribute_group_id=?', $groupId);
        $this->setOrder('sort_order', 'asc');
        return $this;
    }
    
}