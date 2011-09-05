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
 * @TODO
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
    {}

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
        $this->assertTrue($this->checkCurrentPage('manage_customers'), 'Wrong page is opened');
        $searchData = array ('email' => $userData['email']);
        if ($this->search($searchData) == false){
            $this->CustomerHelper()->createCustomer($userData, $addressData);
            $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        }
        $this->assertTrue($this->checkCurrentPage('manage_customers'),
                'After successful customer creation should be redirected to Manage Customers page');
        $data = array_merge($userData, $addressData);
        return $data;
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
     * @test
     */
    public function existingCustomerBillingEqualsShipping($data)
    {
        $orderId = $this->OrderHelper()->createOrderForExistingCustomer(false, 'Default Store View',
                'products', $data['email'], $data, $data, 'visa','Fixed');
        $this->OrderHelper()->coverUpTraces($orderId);
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
     * @test
     */
    public function existingCustomerBillingDiffersFromShipping($data)
    {
        $orderId = $this->OrderHelper()->createOrderForExistingCustomer(false, 'Default Store View',
                'products', $data['email'], $data,
                $this->OrderHelper()->customerAddressGenerator(':alnum:', $addrType = 'shipping', $symNum = 32, TRUE),
                'visa','Fixed');
        $this->OrderHelper()->coverUpTraces($orderId);
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
     * @test
     */
    public function newCustomerBillingEqualsShippingWithoutSave()
    {
        $billingAddress = $this->loadData('new_customer_order_billing_address_reqfields',
                $this->OrderHelper()->customerAddressGenerator(
                ':alnum:', $addrType = 'billing', $symNum = 32, TRUE));
        $billingAddress['billing_save_in_address_book'] = 'no';
        $billingAddress['email'] = $this->generate('email', 32, 'valid');
        $shippingAddress = array(
                'shipping_first_name'           => $billingAddress['billing_first_name'],
                'shipping_last_name'            => $billingAddress['billing_last_name'],
                'shipping_street_address_1'     => $billingAddress['billing_street_address_1'],
                'shipping_city'                 => $billingAddress['billing_city'],
                'shipping_zip_code'             => $billingAddress['billing_zip_code'],
                'shipping_telephone'            => $billingAddress['billing_telephone'],
                'shipping_save_in_address_book' => 'no');
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View', 'products',
                $billingAddress['email'], $billingAddress, $shippingAddress, 'visa','Fixed');
        $this->OrderHelper()->coverUpTraces($orderId, array('email' => $billingAddress['email']));
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
     * @test
     */
    public function newCustomerBillingDiffersFromShippingWithoutSave()
    {
        $billingAddress = $this->loadData('new_customer_order_billing_address_reqfields',
                array(
                    $this->OrderHelper()->customerAddressGenerator(
                    ':alnum:', $addrType = 'billing', $symNum = 32, TRUE),
                    'billing_save_in_address_book' => 'no' ));
        $billingAddress['email'] = $this->generate('email', 32, 'valid');
        $shippingAddress = $this->loadData('new_customer_order_shipping_address_reqfields',
                array(
                    $this->OrderHelper()->customerAddressGenerator(
                    ':alnum:', $addrType = 'shipping', $symNum = 32, TRUE),
                    'shipping_save_in_address_book' => 'no' ));
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View', 'products',
                $billingAddress['email'], $billingAddress, $shippingAddress, 'visa','Fixed');
        $this->OrderHelper()->coverUpTraces($orderId, array('email' => $billingAddress['email']));
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
     * @test
     */
    public function newCustomerBillingEqualsShippingWithSave()
    {
        $billingAddress = $this->loadData('new_customer_order_billing_address_reqfields',
                array(
                    $this->OrderHelper()->customerAddressGenerator(
                    ':alnum:', $addrType = 'billing', $symNum = 32, TRUE),
                    'billing_save_in_address_book' => 'yes' ));
        $billingAddress['email'] = $this->generate('email', 32, 'valid');
        $shippingAddress = array(
                'shipping_first_name'           => $billingAddress['billing_first_name'],
                'shipping_last_name'            => $billingAddress['billing_last_name'],
                'shipping_street_address_1'     => $billingAddress['billing_street_address_1'],
                'shipping_city'                 => $billingAddress['billing_city'],
                'shipping_zip_code'             => $billingAddress['billing_zip_code'],
                'shipping_telephone'            => $billingAddress['billing_telephone'],
                'shipping_save_in_address_book' => 'yes');
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View', 'products',
                $billingAddress['email'], $billingAddress, $shippingAddress, 'visa','Fixed');
        $this->OrderHelper()->coverUpTraces($orderId, array('email' => $billingAddress['email']));
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
     * @test
     */
    public function newCustomerBillingDiffersFromShippingWithSave()
    {
        $billingAddress = $this->loadData('new_customer_order_billing_address_reqfields',
                array(
                    $this->OrderHelper()->customerAddressGenerator(
                    ':alnum:', $addrType = 'billing', $symNum = 32, TRUE),
                    'billing_save_in_address_book' => 'yes' ));
        $billingAddress['email'] = $this->generate('email', 32, 'valid');
        $shippingAddress = $this->loadData('new_customer_order_shipping_address_reqfields',
                array(
                    $this->OrderHelper()->customerAddressGenerator(
                    ':alnum:', $addrType = 'shipping', $symNum = 32, TRUE),
                    'shipping_save_in_address_book' => 'yes' ));
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View', 'products',
                $billingAddress['email'], $billingAddress, $shippingAddress, 'visa','Fixed');
        $this->OrderHelper()->coverUpTraces($orderId, array('email' => $billingAddress['email']));
    }
}
