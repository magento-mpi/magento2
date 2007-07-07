<?php

class Mage_Eav_Model_Mysql4_Entity_Type extends Mage_Core_Model_Resource_Abstract 
{
    public function __construct()
    {
        $this->_setResource('eav');
        $this->_setMainTable('entity_type');
    }
    
    public function loadByName($object, $name)
    {
        $read = $this->getConnection('read');
        
        $select = $read->select()->from($this->getMainTable())
            ->where('entity_name=?', $name);
        $data = $read->fetchRow($select);
        
        if (!$data) {
            return false;
        }
        
        $object->setData($data);

        $this->_afterLoad($object);
        
        return true;
    }
}