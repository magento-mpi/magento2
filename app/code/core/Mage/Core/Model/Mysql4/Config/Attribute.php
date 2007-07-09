<?php

class Mage_Core_Model_Mysql4_Config_Attribute extends Mage_Core_Model_Resource_Abstract 
{
    public function __construct()
    {
        $this->_setResource('core');
        $this->_setMainTable('config_attribute');
    }
}