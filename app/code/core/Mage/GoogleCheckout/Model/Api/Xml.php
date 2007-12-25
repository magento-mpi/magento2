<?php

class Mage_GoogleCheckout_Api_Xml
{

// CHECKOUT
    public function checkoutShoppingCart(Mage_Sales_Model_Quote_Abstract $quote)
    {
        return $this->_call('checkout', 'shoppingCart', array($quote));
    }

// FINANCIAL COMMANDS
    public function authorizeOrder()
    {
        $order = Mage::
    }

    public function chargeOrder()
    {

    }

    public function refundOrder()
    {

    }

    public function cancelOrder()
    {

    }

// FULFILLMENT COMMANDS (ORDER BASED)

    public function processOrder()
    {

    }

    public function deliverOrder()
    {

    }

    public function addTrackingData()
    {

    }

// FULFILLMENT COMMANDS (ITEM BASED)

    public function shipItems()
    {

    }

    public function backorderItems()
    {

    }

    public function returnItems()
    {

    }

    public function cancelItems()
    {

    }

    public function resetItemsShippingInformation()
    {

    }

    public function addMerchantOrderNumber()
    {

    }

    public function sendBuyerMessage()
    {

    }


    public function archiveOrder()
    {

    }

    public function unarchiveOrder()
    {

    }

    public function processNotifications()
    {

    }

    public function processCalculations()
    {

    }

}