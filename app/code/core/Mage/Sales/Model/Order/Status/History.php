<?php

class Mage_Sales_Model_Order_Status_History extends Mage_Core_Model_Abstract
{
    /**
     * Enter description here...
     *
     * @var Mage_Sales_Model_Order_Status
     */
    protected $_status;

    /**
     * Enter description here...
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    protected function _construct()
    {
        $this->_init('sales/order_status_history');
    }

    /**
     * Enter description here...
     *
     * @param Mage_Sales_Model_Order $order
     * @return Mage_Sales_Model_Order_Status_History
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Order_Status
     */
    public function getStatus()
    {
        if (is_null($this->_status)) {
            $this->_status = Mage::getModel('sales/order_status')->load($this->getOrderStatusId());
        }
        return $this->_status;
    }

}
