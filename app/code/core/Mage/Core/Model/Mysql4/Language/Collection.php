<?php

class Mage_Core_Model_Mysql4_Language_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    protected function _construct()
    {
        $this->_init('core/language');
    }
    
    public function toOptionArray()
    {
        return $this->_toOptionArray('language_code', 'language_title', array('title'=>'language_title'));
    }
    
    public function toOptionHash()
    {
        return $this->_toOptionHash('language_code', 'language_title');
    }
}