<?php

class Mage_Sales_Model_Mysql4_Discount
{
    protected $_read;
    protected $_write;
        
    public function __construct($data=array())
    {
        $this->_read = Mage::registry('resources')->getConnection('sales_read');
        $this->_write = Mage::registry('resources')->getConnection('sales_write');
    }
    
    public function getCouponByCode($couponCode)
    {
        $couponTable = Mage::registry('resources')->getTableName('sales_resource', 'discount_coupon');
        $result = $this->_read->fetchRow("select * from ".$couponTable." where coupon_code=?", $couponCode);
        return $result;
    }
}