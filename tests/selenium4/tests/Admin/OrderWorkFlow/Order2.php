<?php

class Admin_OrderWorkFlow_Order2 extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp()
    {
        $this->model = $this->getModel('admin/order');
        $this->setUiNamespace();
    }

    /**
     * Test Suite for testing creation order, invoice, shippment, credit memo and reorder.
     *
     * Conditions:
     * 1. new user(email not used);
     * 2. exist Store View 'SmokeTestStoreView';
     * 3. exist Virtual Product with 'SKU'='VP-01'(filled in ONLY required fields) with price = 100;
     * 4. New Billing Address: ONLY required fields without save;
     * 5. Payment method: Check / Money order(Enabled)
     */
    function testOrderWorkFlow()
    {
        $orderData = array(
            //<--- User --->//
            'user_choise' => 'new',
            'user_email' => 'new_user2@varien.com',
            'user_group' => 'Retailer',
            //<--- Storeview--->//
            'storeview_name' => 'SmokeTestStoreView',
            //<--- Product(s)--->//
            'search_product_sku' => 'VP-01',
            'search_product_name' => 'Virtual Product 01.Required Fields',
            //<--- Billing Address --->//
            'choise_billing_address' => 'new',
            'billing_fname' => 'billing_FName',
            'billing_lname' => 'billing_LName',
            'billing_street' => 'billing_Street',
            'billing_city' => 'billing_City',
            'billing_country' => 'United States',
            'billing_state' => 'California',
            'billing_zip' => 'billing_Zip',
            'billing_tel' => 'billing_Tel',
            //<--- Payment Method --->//
            'payment_method' => 'money_order',
        );
        if ($this->model->doLogin()) {
            $orderNumder = $this->model->doCreateOrder($orderData);
            if ($orderNumder != NULL) {
                $this->model->openOrderAndDoAction($orderNumder, "create_invoice");
                $this->model->openOrderAndDoAction($orderNumder, "create_shippment");
                $this->model->openOrderAndDoAction($orderNumder, "create_credit_memo");
                $this->model->openOrderAndDoAction($orderNumder, "reorder");
            }
        }
    }

}
