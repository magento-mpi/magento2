<?php

class Mage_Eav_Model_Mysql4_Entity_Type extends Mage_Core_Model_Resource_Abstract 
{
    protected function _construct()
    {
        $this->setResourceModel('eav');
        $this->setMainTable('entity_type');
    }
    
    public function loadByName($object, $name)
    {
        return $this->load($object, $name, 'entity_name');
    }
}