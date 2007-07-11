<?php

class Mage_Eav_Model_Entity_Type extends Mage_Core_Model_Abstract
{
    protected $_attributes;
    protected $_sets;
    
    protected function _construct()
    {
        $this->setResourceModel('eav/entity_type');
        $this->setIdFieldName('entity_type_id');
        parent::_construct();
    }

    public function loadByName($name)
    {
        $this->getResource()->loadByName($this, $name);
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
}