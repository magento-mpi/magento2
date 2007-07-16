<?php

class Mage_Eav_Model_Mysql4_Entity_Type_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    public function _construct()
    {
        $this->_init('eav/entity_type');
    }
}