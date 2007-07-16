<?php

class Mage_Eav_Model_Mysql4_Entity_Type extends Mage_Core_Model_Mysql4_Abstract 
{
    protected function _construct()
    {
        $this->_init('eav/entity_type', 'entity_type_id');
    }
    
    public function loadByName($object, $name)
    {
        return $this->load($object, $name, 'entity_name');
    }
}