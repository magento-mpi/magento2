<?php

class Mage_GoogleCheckout_Model_Api extends Varien_Object
{
    protected function _getApi($area)
    {
        return Mage::getModel('googlecheckout/api_xml_'.$area);
    }

// CHECKOUT
    public function checkoutShoppingCart(Mage_Sales_Model_Quote $quote)
    {
        $api = $this->_getApi('checkout')
            ->setQuote($quote)
            ->checkout();
        return $api;
    }

// FINANCIAL COMMANDS
    public function authorizeOrder(Mage_Sales_Model_Order $order)
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->authorizeOrder();
        return $api;
    }

    public function chargeOrder(Mage_Sales_Model_Order $order)
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->chargeOrder();
        return $api;
    }

    public function refundOrder(Mage_Sales_Model_Order $order)
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->refundOrder();
        return $api;
    }

    public function cancelOrder(Mage_Sales_Model_Order $order)
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->cancelOrder();
        return $api;
    }

// FULFILLMENT COMMANDS (ORDER BASED)

    public function processOrder(Mage_Sales_Model_Order $order)
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->processOrder();
        return $api;
    }

    public function deliverOrder(Mage_Sales_Model_Order $order)
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->deliverOrder();
        return $api;
    }

    public function addTrackingData(Mage_Sales_Model_Order $order)
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->addTrackingData();
        return $api;
    }

// FULFILLMENT COMMANDS (ITEM BASED)

    public function shipItems(Mage_Sales_Model_Order $order, array $items)
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

    public function backorderItems()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

    public function returnItems()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

    public function cancelItems()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

    public function resetItemsShippingInformation()
    {

    }

    public function addMerchantOrderNumber()
    {

    }

    public function sendBuyerMessage()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

// OTHER ORDER COMMANDS

    public function archiveOrder()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

    public function unarchiveOrder()
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->setItems($items)
            ->shipItems();
        return $api;
    }

// WEB SERVICE SERVER PROCEDURES

    public function processNotifications()
    {

    }

    public function processCalculations()
    {

    }

}