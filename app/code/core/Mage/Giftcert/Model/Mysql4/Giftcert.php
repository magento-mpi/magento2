<?php

class Mage_Sales_Model_Mysql4_Giftcert
{
    protected $_read;
    protected $_write;
        
    public function __construct($data=array())
    {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('sales_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('sales_write');
    }
    
    public function getGiftcertByCode($giftCode)
    {
        $giftTable = Mage::getSingleton('core/resource')->getTableName('sales/giftcert');
        $result = $this->_read->fetchRow("select * from ".$giftTable." where giftcert_code=?", $giftCode);
        return $result;
    }
}