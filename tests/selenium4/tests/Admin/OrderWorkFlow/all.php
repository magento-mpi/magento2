<?php

class Admin_OrderWorkFlow_all extends TestCaseAbstract {

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
     * Test Suite for testing creation order, invoice, shippment, credit memo
     * and reorder.
     * 
     */
    function testOrderWorkFlow()
    {
        $orderData = array(
            //<--- User --->//
            'user_choise' => 'exist',
            'search_user_email' => 'st9-user@varien.com',
            'search_user_name' => 'FName LName',
            //<--- Storeview--->//
            'storeview_name' => 'SmokeTestStoreView',
            //<--- Product(s)--->//
            'search_product_sku' => 'SP-01',
            'search_product_name' => 'Simple Product 01.Required Fields',
            //<--- Order coupons --->//
            'gift_card_code' => 'NKA0FH-KIC-C06-2M8',
            'coupon_code' => '2106',
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
            'payment_method' => 'paypal_direct',
            'card_name' => '1',
            'card_type' => 'Visa',
            'card_number' => '4000000000000002',
            'card_month' => 12,
            'card_year' => 2015,
            'card_verif_number' => 123,
            //<--- Shipping Method --->//
            'shipping_method_title' => 'Flat Rate',
            'shipping_method' => 'Fixed',
            //<--- Gift Message --->//
            'order_gift_mes_from' => 'order_from',
            'order_gift_mes_to' => 'order_to',
            'order_gift_mes_message' => 'order_message',
            'product_gift_mes_product_name' => 'Simple Product 01.Required Fields',
            'product_gift_mes_from' => 'product_from',
            'product_gift_mes_to' => 'product_to',
            'product_gift_mes_message' => 'product_message',
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
