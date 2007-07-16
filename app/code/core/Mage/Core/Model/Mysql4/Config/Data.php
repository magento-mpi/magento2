<?php

class Mage_Core_Model_Mysql4_Config_Data extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('core/config_data', 'config_id');
    }
}