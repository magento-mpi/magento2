<?php

class Mage_Adminhtml_Model_System_Config_Source_Language
{
    protected $_options;
    
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = Mage::getResourceModel('core/language_collection')->loadData()->toOptionArray();
        }
        return $this->_options;
    }
}
