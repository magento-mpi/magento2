<?php

class Mage_Core_Model_Config_Attribute extends Mage_Core_Model_Abstract 
{
    protected function _construct()
    {
        $this->_setResource('core/config_attribute'); 
        $this->setIdFieldName('attribute_id');
        parent::_construct();
    }
}