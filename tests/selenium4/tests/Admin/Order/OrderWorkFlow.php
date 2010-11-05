<?php

class Admin_Order_OrderWorkFlow extends TestCaseAbstract {

    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('admin/order');
        $this->setUiNamespace();
    }

    /**
     * Test Suite for testing creation order, invoice, shippment, credit memo
     * and reorder.
     * 
     */
    function testOrderWorkFlow()
    {
        $orderData = array(
            //<--- User --->//
            'user_choise' => 'exist',
            'search_user_name' => 'FName LName',
            'search_user_email' => 'st9-user@varien.com',
            //<--- Storeview--->//
            'storeview_name' => 'SmokeTestStoreView',
            //<--- Product(s)--->//
            'search_product_sku' => 'SP-01',
            'search_product_id' => '93',
            'search_product_name' => 'Simple Product 01.Required Fields',
            'search_product_price_from' => '200',
            'search_product_price_to' => '300',
            //<--- Billing Address --->//
            'billing_address_choise' => 'new',
            'billing_fname' => 'FName',
            'billing_lname' => 'LName',
            'billing_company' => 'Varien',
            'billing_street' => '11832 W. Pico Blvd',
            'billing_city' => 'Los Angeles',
            'billing_country' => 'Afghanistan', //'United States',
            'billing_state' => 'Alabama',
            'billing_zip' => '90064',
            'billing_tel' => '(310) 954-8012',
            'billing_fax' => '(310) 919-1189',
            //<--- Shipping Address --->//
            'shipping_address_choise' => 'new',
            'shipping_fname' => 'FName',
            'shipping_lname' => 'LName',
            'shipping_company' => 'Varien',
            'shipping_street' => '11832 W. Pico Blvd',
            'shipping_city' => 'Los Angeles',
            'shipping_country' => 'United States',
            'shipping_state' => 'Alabama',
            'shipping_zip' => '90064',
            'shipping_tel' => '(310) 954-8012',
            'shipping_fax' => '(310) 919-1189',
            //<--- Payment Method --->//
            'payment_method' => 'ccsave',
            'card_name' => '1',
            'card_type' => 'Visa',
            'card_number' => '4000000000000002',
            'card_month' => 12,
            'card_year' => 2015,
            'card_verif_number' => 123,
            //<--- Shipping Method --->//
            'shipping_method_title' => 'Flat Rate',
            'shipping_method' => 'Fixed'
        );

        if ($this->model->doLogin()) {
            $orderNumder = $this->model->doCreateOrder($orderData);
            $this->model->openOrderAndDoAction($orderNumder, "create_invoice");
            $this->model->openOrderAndDoAction($orderNumder, "create_shippment");
            $this->model->openOrderAndDoAction($orderNumder, "create_credit_memo");
            $this->model->openOrderAndDoAction($orderNumder, "reorder");
        }
    }

}
