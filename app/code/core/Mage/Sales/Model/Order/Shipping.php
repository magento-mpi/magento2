<?php

class Mage_Sales_Model_Order_Shipping extends Mage_Core_Model_Abstract
{
    function _construct()
    {
        $this->_init('sales/order_shipping');
    }
}