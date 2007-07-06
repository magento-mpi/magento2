<?php

class Mage_Eav_Model_Entity_Attribute_Source_Table extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('eav/value_option_collection')
                ->setAttributeFilter($this->getAttribute()->getId())
                ->setOrder('sort_order')
                ->load()
                ->toOptionArray();
        }
        return $this->_options;
    }
}