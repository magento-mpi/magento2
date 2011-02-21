<?php

class Admin_OrderWorkFlow_Order5 extends TestCaseAbstract {

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
     * 1. exist user 'admin_user_1@magento.com' without address
     * 2. exist Store View 'SmokeTestStoreView';
     * 3. exist Simple Product with 'SKU'='SP-01'(filled in ONLY required fields) with price = 100;
     * 4. New Billing Address: default fields with save;
     * 5. New Shipping Address: default fields with save;
     * 6. Billing Address != Shipping Address;
     * 7. Payment method: Check / Money order(Enabled)
     * 8. Shipping method: Flat Rate - Fixed (Enabled)
     */
    function testOrderWorkFlow()
    {
        $orderData = array(
            //<--- User --->//
            'user_choise' => 'exist',
            'search_user_email' => 'admin_user_1@magento.com',
            'search_user_name' => 'Without Address',
            'search_registered_from_storeview' => 'Admin',
            //<--- Storeview--->//
            'storeview_name' => 'SmokeTestStoreView',
            //<--- Product(s)--->//
            'search_product_sku' => 'SP-01',
            'search_product_name' => 'Simple Product 01.Required Fields',
            //<--- Billing Address --->//
            'choise_billing_address' => 'new',
            'billing_prefix' => 'Prefix(billing)',
            'billing_initial' => 'Initial(billing)',
            'billing_suffix' => 'Suffix(billing)',
            'billing_fname' => 'FName(billing)',
            'billing_lname' => 'LName(billing)',
            'billing_company' => 'Varien(billing)',
            'billing_street' => '11832 W. Pico Blvd(billing)',
            'billing_city' => 'Los Angeles(billing)',
            'billing_country' => 'United States',
            'billing_state' => 'California',
            'billing_zip' => '90064(billing)',
            'billing_tel' => '(310) 954-8012(billing)',
            'billing_fax' => '(310) 919-1189(billing)',
            'billing_save' => 'Yes',
            //<--- Shipping Address --->//
            'choise_shipping_address' => 'new',
            'shipping_prefix' => 'Prefix(shipping)',
            'shipping_initial' => 'Initial(shipping)',
            'shipping_suffix' => 'Suffix(shipping)',
            'shipping_fname' => 'FName(shipping)',
            'shipping_lname' => 'LName(shipping)',
            'shipping_company' => 'Varien(shipping)',
            'shipping_street' => '11832 W. Pico Blvd(shipping)',
            'shipping_city' => 'Los Angeles(shipping)',
            'shipping_country' => 'United States',
            'shipping_state' => 'Alabama',
            'shipping_zip' => '90064(shipping)',
            'shipping_tel' => '(310) 954-8012(shipping)',
            'shipping_fax' => '(310) 919-1189(shipping)',
            'shipping_save' => 'Yes',
            //<--- Payment Method --->//
            'payment_method' => 'money_order',
            //<--- Shipping Method --->//
            'shipping_method_title' => 'Flat Rate',
            'shipping_method' => 'Fixed',
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
