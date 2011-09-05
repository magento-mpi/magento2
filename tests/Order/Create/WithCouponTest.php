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
 * Tests for creating order with applying coupon.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Order_Create_WithCouponTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     *
     * <p>Log in to Backend.</p>
     * <p>Navigate to 'Manage Products' page</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    protected function assertPreConditions()
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), 'Wrong page is opened');
        $this->addParameter('id', '0');
    }
    /**
     * <p>Precondition test for creating coupon and customer</p>
     *
     * @test
     */
    public function createCouponAndCustomer()
    {
        //Creating coupon
        $this->navigate('manage_shopping_cart_price_rules');
        $this->clickButton('add_new_rule', TRUE);
        $coupon = $this->loadData('coupon_fixed_amount',
                array('from_date' => '10/12/10', 'to_date' => '10/12/15'), array('rule_name','coupon_code'));
        $this->fillForm($coupon);
        $this->saveForm('save_rule');
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'),
                'After successful product creation should be redirected to Manage Products page');
        //Creating customer
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
        $data = array('coupon' => $coupon['coupon_code'] ,'data' => $data);
        return $data;
    }

    /**
     * <p>Creating order with coupon. Coupon amount should be less than Grand Total.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order and select customer coupon can be applied for;</p>
     * <p>3. Select products and add them to the order;</p>
     * <p>4. Apply coupon;</p>
     * <p>5. Fill in all required information</p>
     * <p>6. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created, no error messages appear;</p>
     *
     * @depends createCouponAndCustomer
     * @test
     */
    public function amountLessThanGrandTotal($data)
    {
        $couponCode = $this->loadData('coupon', array('coupon_code' => $data['coupon'], 'success' => true));
        $productData = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        $products = $this->loadData('simple_products_to_add', array());
        $products['product_1']['general_sku'] = $productData['general_sku'];
        $reconfigProduct = $this->loadData('products_to_reconfig_1',
                array('general_sku' => $productData['general_sku']));
        $this->navigate('manage_sales_orders');
        $orderId = $this->OrderHelper()->createOrderForExistingCustomer(false, 'Default Store View',
                $products, $data['data']['email'],
                $data['data'], $data['data'], 'visa','Fixed',
                $reconfigProduct, null, $couponCode);
    }

    /**
     * <p>Creating order with coupon. Coupon amount should be greater than Grand Total.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order and select customer coupon can be applied for;</p>
     * <p>3. Select products and add them to the order;</p>
     * <p>4. Apply coupon;</p>
     * <p>5. Fill in all required information</p>
     * <p>6. Click "Submit Order" button;</p>
     * <p>Expected result:</p>
     * <p>Order is created, no error messages appear;</p>
     *
     * @depends createCouponAndCustomer
     * @test
     */
    public function amountGreaterThanGrandTotal($data)
    {
        $couponCode = $this->loadData('coupon', array('coupon_code' => $data['coupon'], 'success' => true));
        //Precondtions
        $productData = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
        $products = $this->loadData('simple_products_to_add', array());
        $products['product_1']['general_sku'] = $productData['general_sku'];
        $reconfigProduct = $this->loadData('products_to_reconfig_1',
                array('general_sku' => $productData['general_sku']));
        $this->navigate('manage_sales_orders');
        $orderId = $this->OrderHelper()->createOrderForExistingCustomer(false, 'Default Store View',
                $products, $data['data']['email'],
                $data['data'], $data['data'], 'visa','Fixed',
                null, null, $couponCode);
    }

    /**
     * <p>Creating order with coupon. Coupon code is invalid.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to "Manage Orders" page;</p>
     * <p>2. Create new order and select customer coupon can be applied for;</p>
     * <p>3. Select products and add them to the order;</p>
     * <p>4. Apply invalid coupon code;</p>
     * <p>Expected result:</p>
     * <p>Message with error appears;</p>
     * @test
     */
    public function wrongCode()
    {
        $couponCode = $this->loadData('coupon', array('coupon_code' => 'wrong_code', 'success' => false));
        $productData = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'),
                'After successful product creation should be redirected to Manage Products page');
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
        $products = $this->loadData('simple_products_to_add');
        $products['product_1']['general_sku'] = $productData['general_sku'];
        $this->navigate('manage_sales_orders');
        $orderId = $this->OrderHelper()->createOrderForNewCustomer(false, 'Default Store View',
                $products, $billingAddress['email'],
                $billingAddress, $shippingAddress, 'visa','Fixed',
                null, null, $couponCode);
    }
}
