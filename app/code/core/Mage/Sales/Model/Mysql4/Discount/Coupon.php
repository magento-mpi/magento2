<?php

class Mage_Sales_Model_Mysql4_Discount_Coupon
{
    protected $_read;
    protected $_write;
        
    public function __construct($data=array())
    {
        $this->_read = Mage::getSingleton('core/resource')->getConnection('sales_read');
        $this->_write = Mage::getSingleton('core/resource')->getConnection('sales_write');
    }
    
    public function loadByCode($couponCode)
    {
        $couponTable = Mage::getSingleton('core/resource')->getTableName('sales/discount_coupon');
        $result = $this->_read->fetchRow("select * from ".$couponTable." where coupon_code=?", $couponCode);
        Mage::getSingleton('core/resource')->cleanDbRow($result);
        return $result;
    }
}