<?php

abstract class Mage_Sales_Model_Document extends Varien_Data_Object
{
    protected $_docType = 'document';
    
    protected $_entitiesByType;
    protected $_entitiesById;

    public function __construct($data=array())
    {
        $this->reset();
        $this->_setDocumentProperties();
    }
    
    public function reset()
    {
        $this->_entitiesByType = array();
        $this->_entitiesById = array();

        $this->setIsChanged(false);
    }
    
    protected function _setDocumentProperties()
    {
        
    }
    
    public function getDocType()
    {
        return $this->_docType;
    }
    
    public function getIdField()
    {
        return $this->getDocType().'_id';
    }
    
    public function setDocumentId($docId)
    {
        $this->setData($this->getIdField(), $docId);
        return $this;
    }
    
    public function getDocumentId()
    {
        return $this->getData($this->getIdField());
    }

    public function getEntities()
    {
        return $this->_entities;
    }

    public function getEntitiesByType($type)
    {
        if (isset($this->_entitiesByType[$type])) {
            return $this->_entitiesByType[$type];
        }
        return array();        
    }
    
    public function getNewEntityId()
    {
        for ($i=1; $i<1000; $i++) {
            if (!isset($this->_entitiesById[$i])) {
                return $i;
            }
        }
        return false;
    }
    
    public function getEntitiesById($id=null)
    {
        if (is_null($id)) {
            return $this->_entitiesById;
        }
        if (isset($this->_entitiesById[$id])) {
            return $this->_entitiesById[$id];
        }
        return false;  
    }
    
    public function addEntity($entity)
    {
        $id = $entity->getEntityId();
        if (!$id) {
            $id = $this->getNewEntityId();
            $entity->setEntityId($id);
        }
        
        $type = $entity->getEntityType();
        
        $this->_entitiesById[$id] = $entity;
        $this->_entitiesByType[$type][$id] = $entity;

        $entity->setDocument($this);
        
        return $this;
    }
    
    public function removeEntity($entityToRemove)
    {
        if ($entityToRemove instanceof Varien_Data_Object) {
            $entityId = $entityToRemove->getEntityId();
        } else {
            $entityId = (int)$entityToRemove;
        }
        
        $entityToRemove = $this->getEntitiesById($entityId);
        if (empty($entityToRemove)) {
            return $this;
        }
        
        unset($this->_entitiesById[$entityId]);
        unset($this->_entitiesByType[$entityToRemove->getEntityType()][$entityId]);
    }

    public function getEntityTemplates()
    {
        return array();
    }
    
    public function getResource()
    {
        return Mage::getSingleton('sales_resource', $this->getDocType(), array('docType'=>$this->getDocType()));
    }
    
    public function load($documentId)
    {
        $this->setDocumentId($documentId);
        $this->getResource()->load($this);
        return $this;
    }
    
    public function save()
    {
        $this->getResource()->save($this);
        return $this;
    }
    
    public function delete()
    {
        $this->getResource()->delete($this->getDocumentId());
        return $this;
    }
    
    public function getAddressByType($type)
    {
        foreach ($this->getEntitiesByType('address') as $addr) {
            if ($addr->getAddressType()==$type) {
                return $addr;
            }
        }
        return false;
    }
    
}