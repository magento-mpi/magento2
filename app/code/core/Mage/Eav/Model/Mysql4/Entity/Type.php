<?php

class Mage_Eav_Model_Mysql4_Entity_Type extends Mage_Core_Model_Mysql4_Abstract 
{
    protected function _construct()
    {
        $this->_init('eav/entity_type', 'entity_type_id');
    }
    
    public function loadByCode($object, $code)
    {
        return $this->load($object, $code, 'entity_type_code');
    }
}