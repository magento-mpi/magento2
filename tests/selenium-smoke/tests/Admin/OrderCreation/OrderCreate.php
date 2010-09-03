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
        $this->_userEmail = Core::getEnvConfig('frontend/checkout/login/email');
        $this->_storeviewName = Core::getEnvConfig('backend/managestores/storeview/name');
        $this->_productSKU = Core::getEnvConfig('backend/createproduct/sku');
    }


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
        $ordStatus = $this->getText($this->getUiElement("admin/pages/sales/orders/creationInvoice/orderStatus",$ordNum));
        if ($ordStatus != 'Pending') {
            $this->setVerificationErrors("Order has an incorrect status after creation order");
        } else {
            $this->openOrder($ordNum);
            $this->createInvoice();
        }
        $this->clickAndWait($this->getUiElement("admin/topmenu/sales/orders"));
        $ordStatus = $this->getText($this->getUiElement("admin/pages/sales/orders/creationInvoice/orderStatus",$ordNum));
        if ($ordStatus != 'Processing') {
            $this->setVerificationErrors("Order has an incorrect status after creation Invoice");
        } else {
            $this->openOrder($ordNum);
            $this->createShippment();
        }
        $this->clickAndWait($this->getUiElement("admin/topmenu/sales/orders"));
        $ordStatus = $this->getText($this->getUiElement("admin/pages/sales/orders/creationInvoice/orderStatus",$ordNum));
        if ($ordStatus != 'Complete') {
            $this->setVerificationErrors("Order has an incorrect status after creation Shippment");
        } else {
            $this->openOrder($ordNum);
            $this->createCreditMemo();
        }
        $this->clickAndWait($this->getUiElement("admin/topmenu/sales/orders"));
        $ordStatus = $this->getText($this->getUiElement("admin/pages/sales/orders/creationInvoice/orderStatus",$ordNum));
        if ($ordStatus != 'Closed') {
            $this->setVerificationErrors("Order has an incorrect status after creation Credit Memo");
        } else {
            $this->openOrder($ordNum);
            $this->reOrder();
        }
        $ordNum = $this->getText($this->getUiElement("admin/pages/sales/orders/creationOrder/orderNumber"));
        $ordNum = substr($ordNum, 8, 10);
        $this->clickAndWait($this->getUiElement("admin/topmenu/sales/orders"));
        $ordStatus = $this->getText($this->getUiElement("admin/pages/sales/orders/creationInvoice/orderStatus",$ordNum));
        if ($ordStatus != 'Pending') {
            $this->setVerificationErrors("Order has an incorrect status after ReOrder");
        }
        }
    }
}

