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
    
    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $isMultiple = false;
        if (strpos($value, ',')) {
            $isMultiple = true;
            $value = explode(',', $value);
        }
        
        $collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
            ->setAttributeFilter($this->getAttribute()->getId())
            ->setStoreFilter($this->getAttribute()->getEntity()->getStoreId())
            ->setIdFilter($value)
            ->load();
        if ($isMultiple) {
            $values = array();
            foreach ($collection as $item) {
            	$values[] = $item->getValue();
            }
            return $values;
        }
        else {
            if ($item = $collection->getFirstItem()) {
                return $item->getValue();
            }
            return false;
        }
    }
}