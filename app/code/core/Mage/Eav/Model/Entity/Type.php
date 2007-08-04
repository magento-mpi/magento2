<?php

class Mage_Eav_Model_Entity_Type extends Mage_Core_Model_Abstract
{
    protected $_attributes;
    protected $_sets;
    
    protected function _construct()
    {
        $this->_init('eav/entity_type');
    }

    public function loadByCode($code)
    {
        $this->getResource()->loadByCode($this, $code);
        return $this;
    }
    
    public function getAttributeCollection()
    {
        if (empty($this->_attributes)) {
            $this->_attributes = Mage::getModel('eav/entity_attribute')->getResourceCollection()
                ->setEntityTypeFilter($this->getId());
        }
        return $this->_attributes;
    }
    
    public function getAttributeSetCollection()
    {
        if (empty($this->_sets)) {
            $this->_sets = Mage::getModel('eav/entity_attribute_set')->getResourceCollection()
                ->setEntityTypeFilter($this->getId());
        }
        return $this->_sets;
    }
    
    public function fetchNewIncrementId($storeId=null)
    {
        if (!$this->getIncrementModel()) {
            return false;
        }
        
        if (!$this->getIncrementPerStore()) {
            $storeId = 0;
        } elseif (!$storeId) {
            throw Mage::exception('Mage_Eav', 'Valid store_id is expected!');
        }
        
        $entityStoreConfig = Mage::getModel('eav/entity_store')
            ->loadByEntityStore($this->getId(), $storeId);
            
        if (!$entityStoreConfig->getId()) {
            $entityStoreConfig
                ->setEntityTypeId($this->getId())
                ->setStoreId($storeId)
                ->setIncrementPrefix($storeId)
                ->save();
        }
        
        $incrementInstance = Mage::getModel($this->getIncrementModel())
            ->setPrefix($entityStoreConfig->getIncrementPrefix())
            ->setPadLength($entityStoreConfig->getIncrementPadLength())
            ->setPadChar($entityStoreConfig->getIncrementPadChar())
            ->setLastId($entityStoreConfig->getIncrementLastId())
        ;
        
        // do read lock on eav/entity_store to solve potential timing issues
        // (most probably already done by beginTransaction of entity save)
        
        $incrementId = $incrementInstance->getNextId();
        
        $entityStoreConfig->setIncrementLastId($incrementId);

        $entityStoreConfig->save();
        
        return $incrementId;
    }
}