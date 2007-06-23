<?php

class Mage_Core_Model_Mysql4_Store
{
    protected $_read;
    protected $_write;
    protected $_storeTable;
    
    public function __construct()
    {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $this->_storeTable = Mage::getSingleton('core/resource')->getTableName('core/store');
    }
    
    public function load($store)
    {
        if (is_numeric($store)) {
            return $this->_read->fetchRow("select * from ".$this->_storeTable." where store_id=?", $store);
        } elseif (is_string($store)) {
            return $this->_read->fetchRow("select * from ".$this->_storeTable." where store_code=?", $store);
        }
    }
}