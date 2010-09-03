<?php

class Test_Admin_OrderCreation_OrderCreate extends Test_Admin_OrderCreation_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        parent::setUp();

        // Get test parameters
        $this->_userEmail = Core::getEnvConfig('frontend/checkout/register/email');
        $this->_storeviewName = Core::getEnvConfig('backend/managestores/storeview/name');
        $this->_productSKU = Core::getEnvConfig('backend/createproduct/sku');
    }

   var $orderStatus = array(1 => "Pending", 2 => "Pending Payment", 3 => "Processing", 4 => "On Hold", 5 => "Complete", 6 => "Closed", 7 => "Canceled", 8 => "Suspected Fraud", 9 => "Payment Review", 10 => "Pending PayPal", 11 => "Pending Ogone", 12 => "Cancelled Ogone", 13 => "Declined Ogone", 14 => "Processing Ogone Payment", 15 => "Processed Ogone Payment", 16 => "Waiting Authorization");

    /**
     * Test create new order in Admin and Invoice
     *
     */
    function testAdminOrderWorkFlow() {
        // Test Flow
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
        $this->adminOrderCreate($this->_userEmail, $this->_storeviewName, $this->_productSKU);
        if(!$this->isElementPresent($this->getUiElement("admin/pages/sales/orders/creationOrder/messages/orderCreated"),10)) {
            return FALSE;
        } else {
        $ordNum = $this->getText($this->getUiElement("admin/pages/sales/orders/creationOrder/orderNumber"));
        $ordNum = substr($ordNum, 8, 10);
        $this->clickAndWait($this->getUiElement("admin/topmenu/sales/orders"));
        $this->orderStatus($ordNum, $this->orderStatus[1]);
        $this->openOrder($ordNum);
        $this->createInvoice();
        $this->clickAndWait($this->getUiElement("admin/topmenu/sales/orders"));
        $this->orderStatus($ordNum, $this->orderStatus[3]);
        $this->openOrder($ordNum);
        $this->createShippment();
        $this->clickAndWait($this->getUiElement("admin/topmenu/sales/orders"));
        $this->orderStatus($ordNum, $this->orderStatus[5]);
        $this->openOrder($ordNum);
        $this->createCreditMemo();
        $this->clickAndWait($this->getUiElement("admin/topmenu/sales/orders"));
        $this->orderStatus($ordNum, $this->orderStatus[6]);
        $this->openOrder($ordNum);
        $this->reOrder();
        $ordNum = $this->getText($this->getUiElement("admin/pages/sales/orders/creationOrder/orderNumber"));
        $ordNum = substr($ordNum, 8, 10);
        $this->clickAndWait($this->getUiElement("admin/topmenu/sales/orders"));
        $ordStatus = $this->getText($this->getUiElement("admin/pages/sales/orders/creationInvoice/orderStatus",$ordNum));
        $this->orderStatus($ordNum, $this->orderStatus[1]);
        }
    }
}

