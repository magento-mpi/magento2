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
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    protected function assertPreConditions()
    {
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->addParameter('tabName', 'edit/section/payment/');
        $this->clickControl('tab', 'sales_payment_methods');
        $payment = $this->loadData('saved_cc_wo3d_enable');
        $this->fillForm($payment, 'sales_payment_methods');
        $this->saveForm('save_config');
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
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $this->clickButton('add_new_rule');
        $coupon = $this->loadData('coupon_fixed_amount',
                array('from_date' => '10/12/10', 'to_date' => '10/12/15'), array('rule_name','coupon_code'));
        $this->fillForm($coupon);
        $this->saveForm('save_rule');
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        //Creating customer
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
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $productData = $this->loadData('simple_product_for_order', NULL, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $orderData = $this->loadData('order_with_coupon_1',
                array('filter_sku' => $productData['general_sku'], 'coupon_code' => $data['coupon'],
                    'email' => $data['data']['email']));
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $orderData = $this->arrayEmptyClear($orderData);
        $this->addParameter('storeName', $orderData['store_view']);
        $this->orderHelper()->navigateToCreateOrderPage($orderData['customer_data']);
        $this->orderHelper()->addProductsToOrder($orderData['products_to_add']);
        $this->orderHelper()->reconfigProduct($orderData['products_to_reconfigure']);
        $this->assertTrue($this->orderHelper()->applyCoupon($orderData['coupons']),
                $this->messages);
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
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $productData = $this->loadData('simple_product_for_order', NULL, array('general_name', 'general_sku'));
        $couponCode = $this->loadData('coupon', array('coupon_code' => $data['coupon'], 'success' => TRUE));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $orderData = $this->loadData('order_with_coupon_2',
                array('filter_sku' => $productData['general_sku'], 'coupon_code' => $data['coupon'],
                    'email' => $data['data']['email']));
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $orderData = $this->arrayEmptyClear($orderData);
        $this->addParameter('storeName', $orderData['store_view']);
        $this->orderHelper()->navigateToCreateOrderPage($orderData['customer_data']);
        $this->orderHelper()->addProductsToOrder($orderData['products_to_add']);
        $this->assertTrue($this->orderHelper()->applyCoupon($orderData['coupons']),
                'Incorrect message after applying coupon');
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
     *
     * @depends createCouponAndCustomer
     * @test
     */
    public function wrongCode($data)
    {
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $productData = $this->loadData('simple_product_for_order', NULL, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        $orderData = $this->loadData('order_with_coupon_3',
                array('filter_sku' => $productData['general_sku'], 'email' => $data['data']['email']));
        $this->navigate('manage_sales_orders');
        $this->assertTrue($this->checkCurrentPage('manage_sales_orders'), $this->messages);
        $orderData = $this->arrayEmptyClear($orderData);
        $this->addParameter('storeName', $orderData['store_view']);
        $this->orderHelper()->navigateToCreateOrderPage($orderData['customer_data']);
        $this->orderHelper()->addProductsToOrder($orderData['products_to_add']);
        $this->assertTrue($this->orderHelper()->applyCoupon($orderData['coupons']),
                'Incorrect message after applying coupon');
    }
}
