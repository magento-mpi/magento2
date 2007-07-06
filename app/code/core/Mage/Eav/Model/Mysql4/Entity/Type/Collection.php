<?php

class Mage_Eav_Model_Mysql4_Entity_Type_Collection extends Mage_Core_Model_Resource_Collection_Abstract 
{
    public function _construct()
    {
        $this->setModel('eav/entity_type');
    }
}