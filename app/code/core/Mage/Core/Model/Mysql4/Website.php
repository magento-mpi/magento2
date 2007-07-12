<?php

class Mage_Core_Model_Mysql4_Website extends Mage_Core_Model_Resource_Abstract
{
    protected function _construct()
    {
        $this->_init('eav/entity_type', 'website_id');
    }
}