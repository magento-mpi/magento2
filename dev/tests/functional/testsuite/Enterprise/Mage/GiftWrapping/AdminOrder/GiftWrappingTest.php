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
    public function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_disable_all');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_use_default_per_website');
    }

    /**
     * Create Simple Product for tests
     *
     * @return string
     * @test
     */
    public function createSimpleProduct()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');

        return $productData['general_sku'];
    }

    /**
     * Create Gift Wrapping for tests
     *
     * @return array $gwData
     * @test
     */
    public function createGiftWrappingMain()
    {
        //Data
        $gwData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($gwData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        return $gwData;
    }

    /**
     * Create additional Gift Wrapping for tests
     *
     * @return array $gwData
     * @test
     */
    public function createGiftWrappingAdditional()
    {
        //Data
        $gwData = $this->loadDataSet('GiftWrapping', 'edit_gift_wrapping_without_image');
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($gwData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        return $gwData;
    }

    /**
     * <p>TL-MAGE-828: Gift Message for entire Order is allowed</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function giftMessagePerOrderAllowed($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'    => $simpleSku,
                  'gift_messages' => $this->loadDataSet('SalesOrder', 'gift_messages_per_order')));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/order_gift_wrapping_no_message_yes');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderHelper()->verifyGiftMessage($orderData['gift_messages']);
    }

    /**
     * <p>TL-MAGE-968:Gift Message for entire Order is not allowed (message-no; wrapping-yes)</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function giftMessagePerOrderDisabled($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/order_gift_wrapping_yes_message_no');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $this->assertFalse($this->controlIsPresent('pageelement', 'order_gift_message_block'), 'Cannot find the block');
        $this->assertTrue($this->controlIsPresent('pageelement', 'order_gift_wrapping_block'), 'Cannot find the block');
    }

    /**
     * <p>TL-MAGE-834:Gift Wrapping for entire Order is allowed</p>
     *
     * @param array $simpleSku
     * @param array $gwData
     *
     * @test
     * @depends createSimpleProduct
     * @depends createGiftWrappingMain
     */
    public function giftWrappingPerOrderAllowed($simpleSku, $gwData)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'    => $simpleSku,
                  'gift_messages' => $this->loadDataSet('OnePageCheckout', 'order_gift_wrapping',
                      array('order_gift_wrapping_design' => $gwData['gift_wrapping_design']))));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/order_gift_wrapping_yes_message_no');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        $this->assertFalse($this->controlIsPresent('pageelement', 'order_gift_message_block'), 'Cannot find the block');
        $this->assertTrue($this->controlIsPresent('pageelement', 'order_gift_wrapping_block'), 'Cannot find the block');
        $this->orderHelper()->submitOrder();
        //Verifying
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderHelper()->verifyGiftOptions($orderData);
    }

    /**
     * <p>TL-MAGE-984: Gift Wrapping for entire Order is not allowed (wrapping-no; messages-yes)</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function giftWrappingPerOrderDisabled($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'    => $simpleSku,
                  'gift_messages' => $this->loadDataSet('SalesOrder', 'gift_messages_per_order')));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/order_gift_wrapping_no_message_yes');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $this->assertFalse($this->controlIsPresent('pageelement', 'order_gift_wrapping_block'),
            'Cannot find the block');
    }

    /**
     * <p>TL-MAGE-966: Gift Options for entire Order is not allowed</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function giftOptionsPerOrderDisabled($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $this->assertFalse($this->controlIsPresent('pageelement', 'order_gift_message_block'), 'Cannot find the block');
        $this->assertFalse($this->controlIsPresent('pageelement', 'order_gift_wrapping_block'),
            'Cannot find the block');
    }

    /**
     * <p>TL-MAGE-933: Gift Message for Individual Item is allowed</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function giftMessageForIndividualItemAllowed($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'    => $simpleSku,
                  'gift_messages' => $this->loadDataSet('SalesOrder', 'gift_messages_individual',
                      array('sku_product' => $simpleSku))));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_gift_wrapping_no_message_yes');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        $this->orderHelper()->verifyGiftOptions($orderData);
        $this->orderHelper()->submitOrder();
        //Verifying
        $this->orderHelper()->verifyGiftOptions($orderData);
    }

    /**
     * <p>TL-MAGE-987: Gift Message for Individual Items is not allowed(message=no, wrapping=yes)</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function giftMessageForIndividualItemDisabled($simpleSku)
    {
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_gift_wrapping_yes_message_no');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, 'Default Store View');
        $this->orderHelper()->addProductToOrder(array('filter_sku' => $simpleSku));
        $this->addParameter('sku', $simpleSku);
        $this->clickControl('link', 'gift_options', false);
        //Verifying
        $this->assertFalse($this->controlIsPresent('pageelement', 'product_gift_message_block'));
    }

    /**
     * <p>TL-MAGE-938: Gift Wrapping for Individual Item is allowed</p>
     *
     * @param array $simpleSku
     * @param array $gwDataMain
     *
     * @test
     * @depends createSimpleProduct
     * @depends createGiftWrappingMain
     */
    public function giftWrappingForIndividualItemAllowed($simpleSku, $gwDataMain)
    {
        //Data
        $giftWrappingData = $this->loadDataSet('SalesOrder', 'gift_wrapping_for_item',
            array('sku_product' => $simpleSku, 'product_gift_wrapping_design' => $gwDataMain['gift_wrapping_design']));
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku' => $simpleSku, 'gift_messages' => $giftWrappingData));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_gift_wrapping_yes_message_no');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, false);
        //Verifying
        $this->orderHelper()->verifyGiftOptions($orderData);
        $this->orderHelper()->submitOrder();
        $this->orderHelper()->verifyGiftOptions($orderData);
    }

    /**
     * <p>TL-MAGE-989: Gift Options for Individual Items is not allowed</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function giftWrappingPerItemDisabled($simpleSku)
    {
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_gift_wrapping_no_message_yes');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, 'Default Store View');
        $this->orderHelper()->addProductToOrder(array('filter_sku' => $simpleSku));
        $this->addParameter('sku', $simpleSku);
        $this->clickControl('link', 'gift_options', false);
        //Verifying
        $this->assertFalse($this->controlIsPresent('pageelement', 'product_gift_wrapping_block'),
            'Cannot find the block');
    }

    /**
     * <p>TL-MAGE-985: Gift Options for Individual Items is not allowed</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function giftOptionsPerItemDisabled($simpleSku)
    {
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_gift_wrapping_no_message_no');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, 'Default Store View');
        $this->orderHelper()->addProductToOrder(array('filter_sku' => $simpleSku));
        $this->addParameter('sku', $simpleSku);
        //Verification
        $this->assertFalse($this->controlIsPresent('link', 'gift_options'), 'Link is present');
    }

    /**
     * @TODO Move from MAUTOSEL-259 branch to here
     */

    /**
     * <p>TL-MAGE-914: Edit order case</p>
     *
     * @param array $simpleSku
     * @param array $gwDataMain
     *
     * @test
     * @depends createSimpleProduct
     * @depends createGiftWrappingMain
     */
    public function editOrderGiftWrappingAllowed($simpleSku, $gwDataMain)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_gift_options_full', null,
            array('product1' => $simpleSku, 'giftWrappingDesign' => $gwDataMain['gift_wrapping_design']));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_enable_all_default_config');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verification
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButtonAndConfirm('edit', 'confirmation_for_edit');
        $this->orderHelper()->verifyGiftOptions($orderData);
    }

    /**
     * <p>TL-MAGE-923: ReOrder case</p>
     *
     * @param array $simpleSku
     * @param array $gwDataMain
     *
     * @test
     * @depends createSimpleProduct
     * @depends createGiftWrappingMain
     */
    public function reorderOrderGiftWrappingAllowed($simpleSku, $gwDataMain)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_gift_options_full', null,
            array('product1' => $simpleSku, 'giftWrappingDesign' => $gwDataMain['gift_wrapping_design']));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_enable_all_default_config');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verification
        $this->assertMessagePresent('success', 'success_created_order');
        //Steps
        $this->clickButton('reorder');
        //Verification
        $giftOptions =
            $this->loadDataSet('SalesOrder', 'reorder_empty_gift_options', null, array('product1' => $simpleSku));
        $this->orderHelper()->verifyGiftOptions(array('gift_messages' => $giftOptions));
    }

    /**
     * <p>TL-MAGE-929: Gift Receipt is allowed</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function createOrderGiftReceiptAllowed($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'    => $simpleSku, 'customer_email' => $this->generate('email', 32, 'valid'),
                  'gift_messages' => $this->loadDataSet('OnePageCheckout', 'order_gift_wrapping',
                      array('send_gift_receipt' => 'Yes'))));
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_receipt_enable');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verification
        $this->assertMessagePresent('success', 'success_created_order');
        // Verification
        $this->assertTrue($this->controlIsPresent('checkbox', 'send_gift_receipt'), 'Checkbox is absent or unchecked');
    }

    /**
     * <p>TL-MAGE-953: Printed Card is allowed</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function createOrderPrintedCardAllowed($simpleSku)
    {
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_newcustomer_checkmoney_flatrate_usa',
            array('filter_sku'    => $simpleSku, 'customer_email' => $this->generate('email', 32, 'valid'),
                  'gift_messages' => $this->loadDataSet('OnePageCheckout', 'order_gift_wrapping',
                      array('add_printed_card' => 'Yes'))));
        //Configuration
        $this->navigate('system_configuration');
        $printedCardOptions = $this->loadDataSet('GiftMessage', 'gift_printed_card_enable');
        $this->systemConfigurationHelper()->configure($printedCardOptions);
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData);
        //Verification
        $this->assertMessagePresent('success', 'success_created_order');
        $this->assertTrue($this->controlIsPresent('checkbox', 'add_printed_card'), 'Printed Card is not added');
        $this->orderHelper()->verifyPageelement('printed_card_price',
            '$' . $printedCardOptions['tab_1']['configuration']['gift_options']['default_price_for_printed_card']);
        $this->orderHelper()->verifyPageelement('total_printed_card_price',
            '$' . $printedCardOptions['tab_1']['configuration']['gift_options']['default_price_for_printed_card']);
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>TL-MAGE-990: Printed Card is not allowed</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function createOrderPrintedCardNotAllowed($simpleSku)
    {
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_printed_card_disable');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, 'Default Store View');
        $this->orderHelper()->addProductToOrder(array('filter_sku' => $simpleSku));
        //Verification
        //If product is not added 'send_gift_receipt' checkbox will be absent
        $this->addParameter('sku', $simpleSku);
        $this->assertTrue($this->controlIsPresent('field', 'product_qty'), 'Product is not added');
        $this->assertFalse($this->controlIsPresent('checkbox', 'add_printed_card'), 'Checkbox is present');
    }

    /**
     * <p>TL-MAGE-991: Gift Receipt is not allowed</p>
     *
     * @param array $simpleSku
     *
     * @test
     * @depends createSimpleProduct
     */
    public function createOrderGiftReceiptDisabled($simpleSku)
    {
        //Configuration
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_receipt_disable');
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, 'Default Store View');
        $this->orderHelper()->addProductToOrder(array('filter_sku' => $simpleSku));
        //Verification
        //If product is not added 'send_gift_receipt' checkbox will be absent
        $this->addParameter('sku', $simpleSku);
        $this->assertTrue($this->controlIsPresent('field', 'product_qty'), 'Product is not added');
        $this->assertFalse($this->controlIsPresent('checkbox', 'send_gift_receipt'), 'Checkbox is present');
    }
}
