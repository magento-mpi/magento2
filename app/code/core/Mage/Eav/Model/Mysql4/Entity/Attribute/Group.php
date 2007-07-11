<?php

class Mage_Eav_Model_Mysql4_Entity_Attribute_Group extends Mage_Core_Model_Resource_Abstract 
{
    protected function _construct()
    {
        $this->setResourceModel('eav');
        $this->setMainTable('attribute_group');
    }
}