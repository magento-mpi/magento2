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


    public function collectTotals($type='')
    {
        $attrLogicClasses = Mage::getConfig()->getGlobalCollection('salesAttributes', $this->getDocType())->self->children();
        foreach ($attrLogicClasses as $attrName=>$attrConfig) {
            $className = $attrConfig->getClassName();
            if (empty($className)) {
                continue;
            }
            $attrLogic = new $className();
            $arr = $attrLogic->collectTotals($this);
        }
        return $this;
    }
    
    public function getTotals($type='_output')
    {
        $attrLogicClasses = Mage::getConfig()->getGlobalCollection('salesAttributes', $this->getDocType())->self->children();
        
        $totalsArr = array();
        foreach ($attrLogicClasses as $attrName=>$attrConfig) {
            $className = $attrConfig->getClassName();
            if (empty($className)) {
                continue;
            }
            $attrLogic = new $className();
            $arr = $attrLogic->getTotals($this);
            foreach ($arr as $i=>$row) {
                if ('_output'!==$type && ''!==$type && $row['code']!==$type 
                    || '_output'===$type && empty($row['output'])) {
                    unset($arr[$i]);
                }
            }

            $totalsArr = array_merge_recursive($totalsArr, $arr);
        }
        return $totalsArr;
    }
    
    public function hasItems()
    {
        return !empty($this->_entitiesByType['item']);
    }
    
    public function addProduct(Varien_Data_Object $product)
    {
        if (!$product->getAsNewItem()) {
            foreach ($this->getEntitiesByType('item') as $item) {
                if ($item->getProductId()==$product->getProductId()) {
                    $item->setQty($item->getQty()+$product->getQty());
                    $this->collectTotals();
                    return $this;
                }
            }
        } 
        $item = Mage::getModel('sales', $this->getDocType().'_entity_item');
        $item->setEntityType('item')->addData($product->getData());
        $this->addEntity($item);
        $this->collectTotals();
        return $this;
    }
    
    public function updateItems(array $itemsArr)
    {
        foreach ($itemsArr as $id=>$itemUpd) {
            if (!is_numeric($itemUpd['qty']) || $itemUpd['qty']<=0) {
                continue;
            }
            if (!empty($itemUpd['remove'])) {
                $this->removeEntity($id);
            } else {
                $item = $this->getEntitiesById($id);
                if (!$item) {
                    continue;
                }
                $item->setQty($itemUpd['qty']);
            }
        }
        $this->collectTotals();
        return $this;
    }
    
    public function getEntityTemplates()
    {
        $prefix = $this->getDocType()."_entity_";
        return array(
            'item'=>Mage::getModel('sales', $prefix.'item'),
            'address'=>Mage::getModel('sales', $prefix.'address'),
            'payment'=>Mage::getModel('sales', $prefix.'payment'),
        );
    }
    
    public function getResource()
    {
        static $resource;
        if (empty($resource)) {
            $resource = Mage::getModel('sales_resource', $this->getDocType(), array('docType'=>$this->getDocType()));
        }
        return $resource;
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
}