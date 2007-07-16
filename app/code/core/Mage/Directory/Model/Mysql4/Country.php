<?php

class Mage_Directory_Model_Mysql4_Country extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('directory/country', 'country_id');
    }
}