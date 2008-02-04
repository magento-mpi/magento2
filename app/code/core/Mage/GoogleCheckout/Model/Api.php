<?php

class Mage_GoogleCheckout_Model_Api extends Varien_Object
{
    protected function _getApi($area)
    {
        return Mage::getModel('googlecheckout/api_xml_'.$area);
    }

// CHECKOUT
    public function checkout(Mage_Sales_Model_Quote $quote)
    {
        $api = $this->_getApi('checkout')
            ->setQuote($quote)
            ->checkout();
        return $api;
    }

// FINANCIAL COMMANDS
    public function authorize(Mage_Sales_Model_Order $order)
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->authorize();
        return $api;
    }

    public function charge($data)
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->capture();
        return $api;
    }

    public function refund($data)
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->refund();
        return $api;
    }

    public function cancel(Mage_Sales_Model_Order $order)
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->cancel();
        return $api;
    }

// FULFILLMENT COMMANDS (ORDER BASED)

    public function processOrder(Mage_Sales_Model_Order $order)
    {
        $api = $this->_getApi('order')
            ->setOrder($order)
            ->process();
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

    public function processCallback()
    {
        $api = $this->_getApi('callback')->process();
        return $api;
    }

    public function processBeacon()
    {
        $debug = Mage::getModel('googlecheckout/api_debug')->setDir('in')
            ->setUrl('googlecheckout/api/beacon')
            ->setRequestBody($_SERVER['QUERY_STRING'])
            ->save();
    }
}