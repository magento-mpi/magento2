<?php

class Mage_Core_Model_Mysql4_Config_Section_Collection extends Mage_Core_Model_Resource_Collection_Abstract 
{
    public function _construct()
    {
        $this->setModel('core/config_section');
    }
}