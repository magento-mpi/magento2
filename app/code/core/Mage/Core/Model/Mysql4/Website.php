<?php

class Mage_Core_Model_Mysql4_Website extends Mage_Core_Model_Resource_Abstract
{
    protected function _construct()
    {
        $this->_init('core/website', 'website_id');
    }
}