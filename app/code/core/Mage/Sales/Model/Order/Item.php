<?php

class Mage_Sales_Model_Order_Item extends Mage_Core_Model_Abstract
{
    protected $_order;
    
    protected function _construct()
    {
        $this->_init('sales/order_item');
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
    
    public function importQuoteItem(Mage_Sales_Model_Quote_Item $item)
    {
        return $this;
    }
}