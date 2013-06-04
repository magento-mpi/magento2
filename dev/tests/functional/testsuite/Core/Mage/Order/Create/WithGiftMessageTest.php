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
 * Tests with gift messages
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Order_Create_WithGiftMessageTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
    }

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
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');

        return $simple['general_sku'];
    }

    /**
     * <p>Creating order with gift messages for order</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3284
     */
    public function giftMessagePerOrder($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa', array(
            'filter_sku' => $simpleSku,
            'gift_messages' => $this->loadDataSet('SalesOrder', 'gift_messages_per_order')
        ));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_message_for_order_enable');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        $this->addParameter('orderId', $this->defineIdFromUrl());
        $this->orderHelper()->verifyGiftOptions($orderData['gift_messages']);
    }

    /**
     * <p>Creating order with gift messages for products</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3283
     */
    public function giftMessageForProduct($simpleSku)
    {
        //Data
        $gift = $this->loadDataSet('SalesOrder', 'gift_messages_individual', array('sku_product' => $simpleSku));
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku, 'gift_messages' => $gift));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_message_per_item_enable');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        $this->addParameter('orderId', $this->defineIdFromUrl());
        $this->orderHelper()->verifyGiftOptions($orderData['gift_messages']);
    }

    /**
     * <p>Creating order with gift messages for products, but with empty fields in message</p>
     *
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3285
     */
    public function giftMessagesWithEmptyFields($simpleSku)
    {
        //Data
        $gift = $this->loadDataSet('SalesOrder', 'gift_messages_with_empty_fields',
            array('sku_product' => $simpleSku));
        $orderData = $this->loadDataSet('SalesOrder', 'order_physical',
            array('filter_sku' => $simpleSku, 'gift_messages' => $gift));
        $verify = $this->loadDataSet('SalesOrder', 'gift_messages_with_empty_fields_expected',
            array('sku_product' => $simpleSku));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_message_all_enable');
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderHelper()->verifyGiftOptions($verify);
    }
}