<?php

class Mage_Tax_Model_Class_Source_Product extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
	public function getAllOptions()
	{
		if (!$this->_options) {
			$this->_options = Mage::getResourceModel('tax/class_collection')
        		->addFieldToFilter('class_type', 'PRODUCT')
        		->load()->toOptionArray();
		}
		return $this->_options;
	}
	
	public function toOptionArray()
	{
		return $this->getAllOptions();
	}
}