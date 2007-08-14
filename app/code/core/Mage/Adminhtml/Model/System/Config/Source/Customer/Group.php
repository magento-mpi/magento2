<?php

class Mage_Adminhtml_Model_System_Config_Source_Customer_Group
{
    protected $_options;
    
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('customer/group_collection')
            	->addFieldToFilter('customer_group_id', array('neq'=>0))
                ->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}