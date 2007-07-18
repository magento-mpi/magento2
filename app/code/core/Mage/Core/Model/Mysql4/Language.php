<?php

class Mage_Core_Model_Mysql4_Language extends Mage_Core_Model_Mysql4_Abstract 
{
    protected function _construct()
    {
        $this->_init('core/language', 'language_code');
    }
}