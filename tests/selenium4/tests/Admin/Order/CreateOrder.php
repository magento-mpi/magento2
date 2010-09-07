<?php

class Admin_Order_CreateOrder extends TestCaseAbstract
{

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/order');
        $this->setUiNamespace();
    }

    /**
     * Test order status
     */
    function testSiteCreation()
    {
        if ($this->model->doLogin()) {
            $this->model->doCreateOrder();
    }
    }
}
