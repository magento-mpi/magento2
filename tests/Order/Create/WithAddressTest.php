<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test with variations of address
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_Create_WithAddressTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     *
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }
    protected function assertPreConditions()
    {
        $this->addParameter('id', '0');
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->addParameter('tabName', 'edit/section/payment/');
        $this->clickControl('tab', 'sales_payment_methods');
        $payment = $this->loadData('saved_cc_wo3d_enable');
        $this->fillForm($payment, 'sales_payment_methods');
        $this->saveForm('save_config');
    }

    /**
     * <p>Create customer for testing</p>
     * <p>Steps:</p>
     * <p>1. Create new customer;</p>
     * <p>Expected result:</p>
     * <p>Customer should be created;</p>
     *
     * @test
     */
    public function createCustomer()
    {
        $userData = $this->loadData('new_customer', NULL, 'email');
        $addressData = $this->loadData('new_customer_address');
        $this->navigate('manage_customers');
        $this->assertTrue($this->checkCurrentPage('manage_customers'), $this->messages);
        $searchData = array ('email' => $userData['email']);
        if ($this->search($searchData) == false){
            $this->CustomerHelper()->createCustomer($userData, $addressData);
            $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        }
        $this->assertTrue($this->checkCurrentPage('manage_customers'), $this->messages);
        $data = array_merge($userData, $addressData);
        return $data;
    }
    /**
     * @test
     */
    public function createProducts()
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $productData = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        return $productData;
    }
    /**
     * <p>Creating order for existing customer with same billing and shipping addresses.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order and choose existing customer from the list;</p>
     * <p>3. Choose existing address for billing and shipping;</p>
     * <p>4. Fill in all required fields (add products, add payment method information, choose shipping method, etc);</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created, no error messages appear;</p>
     *
     * @depends createCustomer
     * @depends createProducts
     * @test
     */
    public function existingCustomerBillingEqualsShipping($data, $productData)
    {
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $orderData = $this->loadData('order_req_1');
        $orderData['shipping_addr_data']['shipping_same_as_billing_address'] = 'yes';
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderData['customer_data']['email'] = $data['email'];
        $orderId = $this->orderHelper()->createOrder($orderData);
    }

    /**
     * <p>Creating order for existing customer with different billing and shipping addresses.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order and choose existing customer from the list;</p>
     * <p>3. Choose existing address for billing and fill shipping address with new data;</p>
     * <p>4. Fill in all required fields (add products, add payment method information, choose shipping method, etc);</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created, no error messages appear;</p>
     *
     * @depends createCustomer
     * @depends createProducts
     * @test
     */
    public function existingCustomerBillingDiffersFromShipping($data, $productData)
    {
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $shippingAddress = $this->orderHelper()->customerAddressGenerator(
                ':alnum:', $addrType = 'shipping', $symNum = 32, TRUE);
        $orderData = $this->loadData('order_req_1');
        $orderData['shipping_addr_data']['shipping_same_as_billing_address'] = 'no';
        $orderData['shipping_addr_data']+$shippingAddress;
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderData['customer_data']['email'] = $data['email'];
        $orderId = $this->orderHelper()->createOrder($orderData);
    }

    /**
     * <p>Creating order for new customer with same billing and shipping addresses.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order and press "Create New Customer" button;</p>
     * <p>3. Fill in billing and shipping addresses with the same data; Uncheck 'save in address book' checkboxes;</p>
     * <p>4. Fill in all required fields (add products, add payment method information, choose shipping method, etc);</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created, no error messages appear;</p>
     *
     * @depends createProducts
     * @test
     */
    public function newCustomerBillingEqualsShippingWithoutSave($productData)
    {
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $billingAddress = $this->orderHelper()->customerAddressGenerator(
                ':alnum:', $addrType = 'billing', $symNum = 32, TRUE);
        $billingAddress['billing_save_in_address_book'] = 'no';
        $billingAddress['billing_state'] = 'California';
        $shippingAddress = array(
                'shipping_address_choice'       => $billingAddress['billing_address_choice'],
                'shipping_first_name'           => $billingAddress['billing_first_name'],
                'shipping_last_name'            => $billingAddress['billing_last_name'],
                'shipping_street_address_1'     => $billingAddress['billing_street_address_1'],
                'shipping_city'                 => $billingAddress['billing_city'],
                'shipping_state'                => $billingAddress['billing_state'],
                'shipping_zip_code'             => $billingAddress['billing_zip_code'],
                'shipping_telephone'            => $billingAddress['billing_telephone'],
                'shipping_save_in_address_book' => 'no');
        $orderData = $this->loadData('order_req_1');
        $orderData['billing_addr_data'] = $billingAddress;
        $orderData['shipping_addr_data'] = $shippingAddress;
        $orderData['shipping_addr_data']['shipping_same_as_billing_address'] = 'no';
        $orderData['account_data']['customer_email'] = $this->generate('email', 32, 'valid');
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
    }

    /**
     * <p>Creating order for new customer with different billing and shipping addresses.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order and press "Create New Customer" button;</p>
     * <p>3. Fill in billing and shipping addresses with the different data; Uncheck 'save in address book' checkboxes;</p>
     * <p>4. Fill in all required fields (add products, add payment method information, choose shipping method, etc);</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created, no error messages appear;</p>
     *
     * @depends createProducts
     * @test
     */
    public function newCustomerBillingDiffersFromShippingWithoutSave($productData)
    {
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $billingAddress = $this->orderHelper()->customerAddressGenerator(
                ':alnum:', $addrType = 'billing', $symNum = 32, TRUE);
        $billingAddress['billing_save_in_address_book'] = 'no';
        $billingAddress['billing_state'] = 'California';
        $shippingAddress = $this->orderHelper()->customerAddressGenerator(
                ':alnum:', $addrType = 'shipping', $symNum = 32, TRUE);
        $shippingAddress['shipping_state'] = 'California';
        $shippingAddress['shipping_save_in_address_book'] = 'no';
        $shippingAddress['shipping_same_as_billing_address'] = 'no';
        $orderData = $this->loadData('order_req_1');
        $orderData['billing_addr_data'] = $billingAddress;
        $orderData['shipping_addr_data'] = $shippingAddress;
        $orderData['account_data']['customer_email'] = $this->generate('email', 32, 'valid');
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
    }

    /**
     * <p>Creating order for new customer with same billing and shipping addresses.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order and press "Create New Customer" button;</p>
     * <p>3. Fill in billing and shipping addresses with the same data;</p>
     * <p>4. Fill in all required fields (add products, add payment method information, choose shipping method, etc);</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created, no error messages appear;</p>
     *
     * @depends createProducts
     * @test
     */
    public function newCustomerBillingEqualsShippingWithSave($productData)
    {
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $billingAddress = $this->orderHelper()->customerAddressGenerator(
                ':alnum:', $addrType = 'billing', $symNum = 32, TRUE);
        $billingAddress['billing_save_in_address_book'] = 'yes';
        $billingAddress['billing_state'] = 'California';
        $shippingAddress = array(
                'shipping_address_choice'       => $billingAddress['billing_address_choice'],
                'shipping_first_name'           => $billingAddress['billing_first_name'],
                'shipping_last_name'            => $billingAddress['billing_last_name'],
                'shipping_street_address_1'     => $billingAddress['billing_street_address_1'],
                'shipping_city'                 => $billingAddress['billing_city'],
                'shipping_state'                => $billingAddress['billing_state'],
                'shipping_zip_code'             => $billingAddress['billing_zip_code'],
                'shipping_telephone'            => $billingAddress['billing_telephone'],
                'shipping_save_in_address_book' => 'yes',
                'shipping_same_as_billing_address' => 'no');
        $orderData = $this->loadData('order_req_1');
        $orderData['billing_addr_data'] = $billingAddress;
        $orderData['shipping_addr_data'] = $shippingAddress;
        $orderData['account_data']['customer_email'] = $this->generate('email', 32, 'valid');
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
    }

    /**
     * <p>Creating order for new customer with different billing and shipping addresses.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order and press "Create New Customer" button;</p>
     * <p>3. Fill in billing and shipping addresses with the different data;</p>
     * <p>4. Fill in all required fields (add products, add payment method information, choose shipping method, etc);</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created, no error messages appear;</p>
     *
     * @depends createProducts
     * @test
     */
    public function newCustomerBillingDiffersFromShippingWithSave($productData)
    {
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $billingAddress = $this->orderHelper()->customerAddressGenerator(
                ':alnum:', $addrType = 'billing', $symNum = 32, TRUE);
        $billingAddress['billing_state'] = 'California';
        $billingAddress['billing_save_in_address_book'] = 'yes';
        $shippingAddress = $this->orderHelper()->customerAddressGenerator(
                ':alnum:', $addrType = 'shipping', $symNum = 32, TRUE);
        $shippingAddress['shipping_state'] = 'California';
        $shippingAddress['shipping_save_in_address_book'] = 'yes';
        $shippingAddress['shipping_same_as_billing_address'] = 'no';
        $orderData = $this->loadData('order_req_1');
        $orderData['billing_addr_data'] = $billingAddress;
        $orderData['shipping_addr_data'] = $shippingAddress;
        $orderData['account_data']['customer_email'] = $this->generate('email', 32, 'valid');
        $orderData['products_to_add']['product_1']['filter_sku'] = $productData['general_sku'];
        $orderId = $this->orderHelper()->createOrder($orderData);
    }
}
