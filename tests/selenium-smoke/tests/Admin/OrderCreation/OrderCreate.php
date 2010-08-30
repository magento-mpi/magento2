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
     * Test create new order in Admin
     *
     */
    function testAdminOrderCreate() {
        Core::debug("testAdminOrderCreate started");
        // Test Flow
        $this->adminLogin($this->_baseUrl, $this->_userName, $this->_password);
        $this->adminOrderCreate($this->_userEmail, $this->_storeviewName, $this->_productSKU);
        Core::debug("testAdminOrderCreate finished");
        if ($this->isElementPresent($this->getUiElement("admin/pages/sales/orders/creationOrder/messages/orderCreated"),10)){
        Core::debug("testInvoiceCreate started");
        // Test Flow
        $ordNum = $this->getText($this->getUiElement("admin/pages/sales/orders/creationOrder/orderNumber"));
        $ordNum = substr($ordNum, 8, 10);
        $this->openOrder($ordNum);
        $this->createInvoice();
        Core::debug("testInvoiceCreate finished");
        }
    }
}