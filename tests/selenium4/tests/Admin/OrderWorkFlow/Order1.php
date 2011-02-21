<?php

class Admin_OrderWorkFlow_Order1 extends TestCaseAbstract {

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
     * 3. exist Simple Product with 'SKU'='SP-01'(filled in ONLY required fields) with price = 100;
     * 4. New Billing Address: ONLY required fields without save;
     * 5. New Shipping Address: ONLY required fields without save;
     * 6. Billing Address != Shipping Address;
     * 7. Payment method: Check / Money order(Enabled)
     * 8. Shipping method: Flat Rate - Fixed (Enabled)
     * Expected result:
     * INFO:  After adding products: Total 1 product(s) added. Subtotal: $100.00 Discount: $0.00 Row Subtotal: $100.00
     * INFO:  Information about the selected Shipping method: Flat Rate Fixed - $5.00
     * INFO:  Before placing an order: Shipping & Handling (Flat Rate - Fixed) $5.00
     *        Subtotal $100.00 Gift Cards $0.00 Grand Total $105.00
     * INFO:  The order has been created.
     * INFO:  After placing an order: Subtotal $100.00 Shipping & Handling $5.00 Grand Total $105.00
     *        Total Paid $0.00 Total Refunded $0.00 Total Due $105.00
     * INFO:  Order number - <Order Number> Order Status - Pending
     * INFO:  The invoice has been created.
     * INFO:  After create_invoice: Subtotal $100.00  Shipping & Handling $5.00
     *        Grand Total $105.00 Total Paid $105.00 Total Refunded $0.00 Total Due $0.00
     * INFO:  Order Status - Processing
     * INFO:  The shipment has been created.
     * INFO:  After create_shippment: Subtotal $100.00 Shipping & Handling $5.00 Grand Total $105.00
     *        Total Paid $105.00 Total Refunded $0.00 Total Due $0.00
     * INFO:  Order Status - Complete
     * INFO:  The credit memo has been created.
     * INFO:  After create_credit_memo: Subtotal $100.00 Shipping & Handling $5.00 Grand Total $105.00
     *        Total Paid $105.00 Total Refunded $105.00 Total Due $0.00
     * INFO:  Order Status - Closed
     * INFO:  The order has been created.
     * INFO:  After reorder: Subtotal $100.00 Shipping & Handling $5.00 Grand Total $105.00 Total Paid $0.00
     *        Total Refunded $0.00 Total Due $105.00
     * INFO:  Order Status - Pending
     */
    function testOrderWorkFlow()
    {
        $orderData = array(
            //<--- User --->//
            'user_choise' => 'new',
            'user_email' => 'new_user1@varien.com',
            'user_group' => 'Retailer',
            //<--- Storeview--->//
            'storeview_name' => 'SmokeTestStoreView',
            //<--- Product(s)--->//
            'search_product_sku' => 'SP-01',
            'search_product_name' => 'Simple Product 01.Required Fields',
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
            //<--- Shipping Address --->//
            'choise_shipping_address' => 'new',
            'shipping_fname' => 'shipping_FName',
            'shipping_lname' => 'shipping_LName',
            'shipping_street' => 'shipping_Street',
            'shipping_city' => 'shipping_City',
            'shipping_country' => 'United States',
            'shipping_state' => 'Alabama',
            'shipping_zip' => 'shipping_Zip',
            'shipping_tel' => 'shipping_Tel',
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
