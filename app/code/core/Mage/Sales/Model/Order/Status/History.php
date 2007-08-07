<?php

class Mage_Sales_Model_Order_Status_History extends Mage_Core_Model_Abstract
{
    protected $_order;

    protected function _construct()
    {
        $this->_init('sales/order_status_history');
    }

    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    public function getOrder()
    {
        return $this->_order;
    }

}
