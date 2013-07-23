<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_GiftWrapping
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for invoice, shipment and credit memo with gift options
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_GiftWrapping_AdminOrder_GiftWrappingTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('SingleStoreMode/disable_single_store_mode');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_disable_all');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_use_default_per_website');
    }

    public function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_disable_all');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_use_default_per_website');
    }

    /**
     * Create Simple Product and Gift Wrapping for tests
     *
     * @return string
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        $giftWrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        //Steps and Verifying
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');

        return array('simple' => $productData['general_sku'], 'wrapping' => $giftWrapping['gift_wrapping_design']);
    }

    /**
     * <p>TL-MAGE-966: Gift Options for entire Order is not allowed</p>
     * <p>TL-MAGE-985: Gift Options for Individual Items is not allowed</p>
     * <p>TL-MAGE-990: Printed Card is not allowed</p>
     * <p>TL-MAGE-991: Gift Receipt is not allowed</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftOptionsPerOrderDisabled($testData)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $testData['simple']));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->doAdminCheckoutSteps($orderData);
        //Verifying
        $this->assertFalse($this->controlIsVisible('pageelement', 'order_gift_message_block'),
            'Gift Message for the Entire Order is available');
        $this->assertFalse($this->controlIsVisible('pageelement', 'order_gift_wrapping_block'),
            'Gift Wrapping for the Entire Order is available');
        $this->addParameter('sku', $testData['simple']);
        $this->assertFalse($this->controlIsVisible('link', 'gift_options'),
            'Link for adding Gift Options for Order Items is present');
        $this->assertFalse($this->controlIsVisible('checkbox', 'add_printed_card'),
            'Printed Card checkbox is present');
        $this->assertFalse($this->controlIsVisible('checkbox', 'send_gift_receipt'),
            'Gift Receipt checkbox is present');
    }

    /**
     * <p>TL-MAGE-828: Gift Message for entire Order is allowed</p>
     * <p>TL-MAGE-984: Gift Wrapping for entire Order is not allowed (wrapping-no; messages-yes)</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftMessagePerOrderAllowedWrappingDisabled($testData)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa', array(
            'filter_sku' => $testData['simple'],
            'gift_messages' => $this->loadDataSet('SalesOrder', 'gift_messages_per_order')
        ));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/order_gift_wrapping_no_message_yes');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->doAdminCheckoutSteps($orderData);
        $this->assertFalse($this->controlIsVisible('pageelement', 'order_gift_wrapping_block'),
            'Gift Wrapping for the Entire Order is available');
        $this->orderHelper()->submitOrder();
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderHelper()->verifyGiftOptions($orderData['gift_messages']);
    }

    /**
     * <p>TL-MAGE-968:Gift Message for entire Order is not allowed (message-no; wrapping-yes)</p>
     * <p>TL-MAGE-834:Gift Wrapping for entire Order is allowed</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftMessagePerOrderDisabledWrappingAllowed($testData)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa', array(
            'filter_sku' => $testData['simple'],
            'gift_messages' => $this->loadDataSet('OnePageCheckout', 'order_gift_wrapping',
                array('order_gift_wrapping_design' => $testData['wrapping'])
            )
        ));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/order_gift_wrapping_yes_message_no');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->doAdminCheckoutSteps($orderData);
        $this->assertFalse($this->controlIsVisible('pageelement', 'order_gift_message_block'),
            'Gift Message for the Entire Order is available');
        $this->assertTrue($this->controlIsVisible('pageelement', 'order_gift_wrapping_block'),
            'Gift Wrapping for the Entire Order is not available');
        $this->orderHelper()->submitOrder();
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderHelper()->verifyGiftOptions($orderData['gift_messages']);
    }

    /**
     * <p>TL-MAGE-933: Gift Message for Individual Item is allowed</p>
     * <p>TL-MAGE-989: Gift Wrapping for Individual Items is not allowed</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftMessageForIndividualItemAllowedWrappingDisabled($testData)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa', array(
            'filter_sku' => $testData['simple'],
            'gift_messages' => $this->loadDataSet('SalesOrder', 'gift_messages_individual',
                array('sku_product' => $testData['simple']))
        ));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_gift_wrapping_no_message_yes');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->doAdminCheckoutSteps($orderData);
        //Verifying
        $this->clickControl('link', 'gift_options', false);
        $this->waitForControlVisible('fieldset', 'gift_options');
        $this->assertFalse($this->controlIsVisible('pageelement', 'item_gift_wrapping_block'),
            'Gift Wrapping for Order Items is available');
        $this->clickButton('ok', false);
        $this->pleaseWait();
        //Steps
        $this->orderHelper()->submitOrder();
        //Verifying
        $this->orderHelper()->verifyGiftOptions($orderData['gift_messages']);
    }

    /**
     * <p>TL-MAGE-987: Gift Message for Individual Items is not allowed(message=no, wrapping=yes)</p>
     * <p>TL-MAGE-938: Gift Wrapping for Individual Item is allowed</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftMessageForIndividualItemDisabledWrappingAllowed($testData)
    {
        $this->markTestIncomplete('MAGETWO-11598');
        //Data
        $wrapping = $this->loadDataSet('SalesOrder', 'gift_wrapping_for_item',
            array('sku_product' => $testData['simple'], 'item_gift_wrapping_design' => $testData['wrapping']));
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $testData['simple'], 'gift_messages' => $wrapping));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_gift_wrapping_yes_message_no');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->doAdminCheckoutSteps($orderData);
        //Verifying
        $this->clickControl('link', 'gift_options', false);
        $this->waitForControlVisible('fieldset', 'gift_options');
        $this->assertFalse($this->controlIsVisible('pageelement', 'item_gift_message_block'),
            'Gift Messages for Order Items is available');
        $this->clickButton('ok', false);
        $this->pleaseWait();
        //Steps
        $this->orderHelper()->submitOrder();
        //Verifying
        $this->orderHelper()->verifyGiftOptions($orderData['gift_messages']);
    }

    /**
     * <p>TL-MAGE-914: Edit order case</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function editOrderAllGiftOptionsAllowed($testData)
    {
        $this->markTestIncomplete('MAGETWO-11766');
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_gift_options_full', null,
            array('product1' => $testData['simple'], 'giftWrappingDesign' => $testData['wrapping']));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_enable_all');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verification
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButtonAndConfirm('edit', 'confirmation_for_edit');
        $this->orderHelper()->verifyGiftOptions($orderData['gift_messages']);
    }

    /**
     * <p>TL-MAGE-923: ReOrder case</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function reorderOrderAllGiftOptionsAllowed($testData)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_gift_options_full', null,
            array('product1' => $testData['simple'], 'giftWrappingDesign' => $testData['wrapping']));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_enable_all');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verification
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButton('reorder');
        //Verification
        $giftOptions = $this->loadDataSet('SalesOrder', 'reorder_empty_gift_options', null,
            array('product1' => $testData['simple']));
        $this->orderHelper()->verifyGiftOptions(array('gift_messages' => $giftOptions));
    }

    /**
     * <p>TL-MAGE-929: Gift Receipt is allowed</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function createOrderGiftReceiptAllowed($testData)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa', array(
            'filter_sku' => $testData['simple'],
            'gift_messages' => $this->loadDataSet('OnePageCheckout', 'order_gift_wrapping', array(
                'send_gift_receipt' => 'Yes'
            ))
        ));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_receipt_enable');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verification
        $this->assertMessagePresent('success', 'success_created_order');
        // Verification
        $this->assertTrue($this->controlIsVisible('checkbox', 'send_gift_receipt'),
            'Checkbox send_gift_receipt is absent');
        $this->assertTrue($this->getControlAttribute('checkbox', 'send_gift_receipt', 'selectedValue'),
            'Checkbox send_gift_receipt is unchecked');
    }

    /**
     * <p>TL-MAGE-953: Printed Card is allowed</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function createOrderPrintedCardAllowed($testData)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa', array(
            'filter_sku' => $testData['simple'],
            'gift_messages' => $this->loadDataSet('OnePageCheckout', 'order_gift_wrapping', array(
                'add_printed_card' => 'Yes'
            ))
        ));
        $printedCardOptions = $this->loadDataSet('GiftMessage', 'gift_printed_card_enable');
        $price = $printedCardOptions['tab_1']['configuration']['gift_options']['default_price_for_printed_card'];
        $price = '$' . $price . '.00';
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($printedCardOptions);
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        $this->assertMessagePresent('success', 'success_created_order');
        //Verification
        $this->assertTrue($this->controlIsVisible('checkbox', 'add_printed_card'),
            'Checkbox add_printed_card is absent');
        $this->assertTrue($this->getControlAttribute('checkbox', 'add_printed_card', 'selectedValue'),
            'Checkbox add_printed_card is unchecked');
        $this->assertEquals($price, $this->getControlAttribute('pageelement', 'printed_card_price', 'text'));
        $this->assertEquals($price, $this->getControlAttribute('pageelement', 'total_printed_card_price', 'text'));
    }
}
