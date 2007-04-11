<?php

class Mage_Sales_Model_Quote extends Varien_Data_Object
{
    protected $_entities = null;
    protected $_attributes = null;
    protected $_entitiesByType = array();
    protected $_entitiesById = array();
    protected $_newEntityId = 0;
    protected $_newAttributeId = 0;
    
    public function __construct($data=array())
    {
        $this->reset();
    }
    
    public function reset()
    {
        $this->_entities = Mage::getModel('sales', 'quote_entity_collection');
        $this->_attributes = Mage::getModel('sales', 'quote_attribute_collection');
        $this->_entitiesByType = array();
        $this->_entitiesById = array();
        $this->_newEntityId = 0;
        $this->_newAttributeId = 0;

        $this->resetChanged(false);
    }
    
    public function getNewEntityId()
    {
        return --$this->_newEntityId;
    }
    
    public function lastNewEntityId()
    {
        return $this->_newEntityId;
    }

    public function getNewAttributeId()
    {
        return --$this->_newAttributeId;
    }
    
    public function lastNewAttributeId()
    {
        return $this->_newAttributeId;
    }
    
    public function getAttributes()
    {
        return $this->_attributes;
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
        return false;        
    }
    
    public function getEntityById($id)
    {
        if (isset($this->_entitiesById[$id])) {
            return $this->_entitiesById[$id];
        }
        return false;  
    }
    
    public function getQuoteEntity()
    {
        if (empty($this->_entitiesByType['quote'])) {
            $this->addEntity('quote', Mage::getModel('sales', 'quote_entity'));
        }
        foreach ($this->getEntitiesByType('quote') as $quote) {
            return $quote;
        }
    }
    
    public function addEntity($type, Varien_Data_Object $source=null)
    {
        $entity = Mage::getModel('sales', 'quote_entity');
        $entity->setQuote($this);
        
        if (!is_null($source)) {
            $entity->importDataObject($source, $type);
        }
        
        if (!$entity->hasQuoteEntityId()) {
            $entity->setQuoteEntityId($this->getNewEntityId());
        }
        $id = $entity->getQuoteEntityId();
        
        $this->_entities->addItem($entity);
        $this->_entitiesById[$id] = $entity;
        $this->_entitiesByType[$type][$id] = $entity;
        
        return $this;
    }
    
    public function removeEntity($entityToRemove)
    {
        if ($entityToRemove instanceof Mage_Sales_Model_Quote_Entity) {
            $entityId = $entity->getQuoteEntityId();
        } else {
            $entityId = (int)$entityToRemove;
        }
        
        $entityToRemove = $this->getEntityById($entityId);
        if (empty($entityToRemove)) {
            return $this;
        }
        
        unset($this->_entitiesById[$entityId]);
        unset($this->_entitiesByType[$entityToRemove->getEntityType()][$entityId]);
        
        $entities = $this->getEntities();
        foreach ($entities as $key=>$entity) {
            if ($entity->getQuoteEntityId()===$entityToRemove->getQuoteEntityId()) {
                unset($entities[$key]);
            }
        }
    }
    
    protected function _afterLoad()
    {
        foreach ($this->_entities->getItems() as $entity) {
            $type = $entity->getEntityType();
            $id = $entity->getQuoteEntityId();
            $this->_entitiesByType[$type][$id] = $entity;
            $this->_entitiesById[$id] = $entity;
            $entity->setQuote($this);
        }
        
        foreach ($this->_attributes->getItems() as $attr) {
            $id = $attr->getQuoteEntityId();
            $code = $attr->getAttributeCode();
            $this->_entitiesById[$id]->setAttribute($attr);
            $attr->setEntity($this->getEntityById($id));
        }
        return $this;
    }
    
    public function getAddressByType($type)
    {
        foreach ($this->getEntity('address') as $addr) {
            if ($addr->getAttribute('type/varchar')==$type) {
                return $addr;
            }
        }
    }
    
    public function collectTotals($type='')
    {
        $attrLogicClasses = Mage::getConfig()->getGlobalCollection('salesQuoteAttributes')->children();
        $totalsArr = array();
        foreach ($attrLogicClasses as $attrName=>$attrConfig) {
            $className = $attrConfig->getClassName();
            $attrLogic = new $className();
            $arr = $attrLogic->collectTotals($this);
            foreach ($arr as $i=>$row) {
                if (''!==$type && $row['code']!==$type || '_output'===$type && empty($row['output'])) {
                    unset($arr[$i]);
                }
            }
            $totalsArr = array_merge_recursive($totalsArr, $arr);
        }
        return $totalsArr;
    }
    
    public function getItemsAsArray($fields=null)
    {
        $arr = array();
        if (is_null($fields)) {
            $fields = Mage::getModel('sales', 'quote_entity')->getDefaultType('', 'item');
        }
        
        $items = $this->getEntitiesByType('item');
        if (is_array($items) && !empty($items)) {
            foreach ($items as $id=>$item) {
                $arr[$id]['id'] = $id;
                $types = $item->getDefaultAttributeType();
                foreach ($item->getAttributes() as $attr) {
                    $code = $attr->getAttributeCode();
                    $arr[$id][$code] = $attr->getData('attribute_'.$types[$code]);
                }
            }
        }
               
        return $arr;
    }
}