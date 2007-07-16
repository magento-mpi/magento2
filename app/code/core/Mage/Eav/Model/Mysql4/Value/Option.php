<?php

class Mage_Eav_Model_Mysql4_Value_Option extends Mage_Core_Model_Mysql4_Abstract 
{
    protected function _construct()
    {
        $this->setResourceModel('eav');
        $this->setMainTable('value_option');
    }
}