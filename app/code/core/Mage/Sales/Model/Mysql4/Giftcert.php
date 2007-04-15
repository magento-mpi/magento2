<?php

class Mage_Sales_Model_Mysql4_Giftcert
{
    protected $_read;
    protected $_write;
        
    public function __construct($data=array())
    {
        $this->_read = Mage::registry('resources')->getConnection('sales_read');
        $this->_write = Mage::registry('resources')->getConnection('sales_write');
    }
    
    public function getGiftcertByCode($giftCode)
    {
        $giftTable = Mage::registry('resources')->getTableName('sales_resource', 'giftcert');
        $result = $this->_read->fetchRow("select * from ".$giftTable." where giftcert_code=?", $giftCode);
        return $result;
    }
}