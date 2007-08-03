<?php

class Mage_Eav_Model_Entity_Attribute_Source_Table extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('eav/entity_attribute_option_collection')
                ->setAttributeFilter($this->getAttribute()->getId())
                ->setStoreFilter($this->getAttribute()->getEntity()->getStoreId())
                ->setOrder('value', 'asc')
                ->load()
                ->toOptionArray();
                
            array_unshift($this->_options, array('label'=>'', 'value'=>''));
        }
        return $this->_options;
    }
}