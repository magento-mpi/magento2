<?php

class Mage_Core_Model_Config_Section extends Mage_Core_Model_Abstract 
{
    protected function _construct()
    {
        $this->_setResource('core/config_section'); 
        $this->setIdFieldName('section_id');
        parent::_construct();
    }
}