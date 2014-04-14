<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Order
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for creating order with applying coupon.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Order_Create_WithCouponTest extends Mage_Selenium_TestCase
{
    public function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Create Simple Product for tests</p>
     *
     * @return string
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');

        return $simple['general_sku'];
    }

    /**
     * <p>Creating order with coupon. Coupon amount should be less than Grand Total.</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3269
     */
    public function amountLessThanGrandTotal($simpleSku)
    {
        //Data
        $coupon = $this->loadDataSet('SalesOrder', 'coupon_fixed_amount', array('discount_amount' => 5));
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa', array(
            'filter_sku' => $simpleSku,
            'coupon_1' => $coupon['info']['coupon_code']
        ));
        //Steps
        $this->navigate('manage_shopping_cart_price_rules');
        $this->priceRulesHelper()->createRule($coupon);
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
    }

    /**
     * <p>Creating order with coupon. Coupon amount should be greater than Grand Total.</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3268
     */
    public function amountGreaterThanGrandTotal($simpleSku)
    {
        //Data
        $coupon = $this->loadDataSet('SalesOrder', 'coupon_fixed_amount', array('discount_amount' => 130));
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa', array(
            'filter_sku' => $simpleSku,
            'coupon_1' => $coupon['info']['coupon_code']
        ));
        unset($orderData['payment_data']);
        //Steps
        $this->navigate('manage_shopping_cart_price_rules');
        $this->priceRulesHelper()->createRule($coupon);
        $this->assertMessagePresent('success', 'success_saved_rule');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
    }

    /**
     * <p>Creating order with coupon. Coupon code is invalid.</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3270
     */
    public function wrongCode($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        $this->orderHelper()->addProductToOrder($orderData['products_to_add']['product_1']);
        $this->orderHelper()->applyCoupon('wrong_code', false);
        $this->addParameter('code', 'wrong_code');
        $this->assertMessagePresent('error', 'invalid_coupon_code');
    }
}