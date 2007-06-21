<?php

class Mage_Core_Model_Mysql4_Website
{
    protected $_read;
    protected $_write;
    protected $_websiteTable;
    
    public function __construct()
    {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('core_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('core_write');
        $this->_websiteTable = Mage::getSingleton('core/resource')->getTableName('core/website');
    }
    
    public function load($website)
    {
        if (is_numeric($website)) {
            return $this->_read->fetchRow("select * from ".$this->_websiteTable." where website_id=?", $website);
        } elseif (is_string($website)) {
            return $this->_read->fetchRow("select * from ".$this->_websiteTable." where website_code=?", $website);
        }
    }
}