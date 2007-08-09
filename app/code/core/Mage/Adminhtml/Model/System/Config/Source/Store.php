<?php

class Mage_Adminhtml_Model_System_Config_Source_Store
{
    protected $_options;
    
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('core/store_collection')
                ->addFieldToFilter('store_id', array('neq'=>0))
                ->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}