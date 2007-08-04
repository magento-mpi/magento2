<?php

class Mage_Eav_Model_Entity_Store extends Mage_Core_Model_Abstract 
{
    protected function _construct()
    {
        $this->_init('eav/entity_store');
    }
    
    public function loadByEntityStore($entityTypeId, $storeId)
    {
        $this->getResource()->loadByEntityStore($this, $entityTypeId, $storeId);
        return $this;
    }
}