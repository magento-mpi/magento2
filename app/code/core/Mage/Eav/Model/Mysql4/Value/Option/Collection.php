<?php

class Mage_Eav_Model_Mysql4_Value_Option_Collection extends Mage_Core_Model_Resource_Collection_Abstract 
{
    public function _construct()
    {
        $this->setModel('eav/value_option');
    }
    
    public function setAttributeFilter($setId)
    {
        $this->getSelect()->where('main_table.attribute_id=?', $setId);
        return $this;
    }
}