<?php

class Mage_Eav_Model_Mysql4_Entity_Type extends Mage_Core_Model_Resource_Abstract 
{
    public function __construct()
    {
        $this->_setResource('eav');
        $this->_setMainTable('entity_type');
    }
}