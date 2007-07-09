<?php

class Mage_Core_Model_Config_Group extends Mage_Core_Model_Abstract 
{
    protected function _construct()
    {
        $this->_setResource('core/config_group'); 
        $this->setIdFieldName('group_id');
        parent::_construct();
    }
}