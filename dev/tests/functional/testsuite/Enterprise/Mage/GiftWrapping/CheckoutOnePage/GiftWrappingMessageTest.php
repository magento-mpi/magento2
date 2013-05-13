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
 * Tests Gift Wrapping.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_GiftWrapping_CheckoutOnePage_GiftWrappingMessageTest extends Mage_Selenium_TestCase
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

    protected function tearDownAfterTest()
    {
        $this->frontend();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        //Load default application settings
        $this->getConfigHelper()->getConfigAreas(true);
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_disable_all');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_use_default_per_website');
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @return array $productData
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $wrappingNoImage = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $wrappingWithImage = $this->loadDataSet('GiftWrapping', 'gift_wrapping_with_image');
        //Steps and Verification
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');

        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($wrappingNoImage);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($wrappingWithImage);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');

        return array(
            'img' => $wrappingWithImage,
            'noImg' => $wrappingNoImage,
            'simple_name' => $productData['general_name'],
            'simple_sku' => $productData['general_sku'],
            'user' => array('email' => $userData['email'], 'password' => $userData['password'])
        );
    }

    /**
     * @TestlinkId TL-MAGE-850
     * <p>Gift Message for entire Order is allowed</p>
     * <p>Verify that when "Allow Gift Messages on Order Level" setting is set to "Yes",
     * customer has an ability to add Gift Message to entire Order during OnePageCheckout,
     * prompt for which should be present on Shipping Method step after checking the appropriate checkbox</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftMessageForEntireOrder($testData)
    {
        //Data
        $options = $this->loadDataSet('OnePageCheckout', 'order_gift_message_with_gift_wrapping');
        $checkout = $this->loadDataSet('OnePageCheckout', 'gift_data_general',
            array('add_gift_options' => $options),
            array('add_product_1' => $testData['simple_name'])
        );
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_message_for_order_enable');
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkout);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $this->orderHelper()->verifyGiftOptions(array('entire_order' => $options['entire_order']['gift_message']));
    }

    /**
     * @TestlinkId TL-MAGE-881
     * @TestlinkId TL-MAGE-905
     * <p>Gift Message for entire Order is not allowed (message-no; wrapping-no)</p>
     * <p>Gift Message for Individual Items is not allowed (message-no; wrapping-no)</p>
     * <p>Verify that when "Allow Gift Messages on Order Level" setting is set to "No",
     * customer is not able to add Gift Message to entire Order during OnePageCheckout</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftMessageIsNotAllowedWrappingNo($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('add_product_1' => $testData['simple_name']));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_printed_card_enable');
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        foreach ($checkoutData['products_to_add'] as $data) {
            $this->productHelper()->frontOpenProduct($data['general_name']);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getParsedMessages());
        $this->clickButton('proceed_to_checkout');
        $this->checkoutOnePageHelper()->frontFillOnePageBillingAddress($checkoutData['billing_address_data']);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        //Verification
        $this->assertFalse($this->controlIsVisible('checkbox', 'gift_option_for_order'),
            'It is possible to add gift options for the Entire Order');
        $this->assertFalse($this->controlIsVisible('checkbox', 'gift_option_for_item'),
            'It is possible to add Gift Options for Individual Items');
        $this->assertFalse($this->controlIsVisible('checkbox', 'send_gift_receipt'),
            'Gift Receipt is available');
        $this->assertFalse($this->controlIsPresent('link', 'add_order_gift_message'),
            'Gift Messages on Order Level is available');
        $this->assertFalse($this->controlIsPresent('dropdown', 'order_gift_wrapping_design'),
            'Gift Wrapping on Order Level is available');
        $this->addParameter('productName', $testData['simple_name']);
        $this->assertFalse($this->controlIsPresent('link', 'add_item_gift_message'),
            'Gift Messages for Order Items is available');
        $this->assertFalse($this->controlIsPresent('dropdown', 'item_gift_wrapping_design'),
            'Gift Wrapping for Order Items is available');
    }

    /**
     * @TestlinkId TL-MAGE-891
     * @TestlinkId TL-MAGE-906
     * <p>Gift Message for entire Order is not allowed (message-no; wrapping-yes)</p>
     * <p>Gift Message for Individual Items is not allowed (message-no; wrapping-yes)</p>
     * <p>Verify that when "Allow Gift Messages on Order Level" setting is set to "No",
     * customer is not able to add Gift Message to entire Order during OnePageCheckout</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftMessageIsNotAllowedWrappingYes($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('add_product_1' => $testData['simple_name']));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_wrapping_for_order_and_per_item_enable');
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        foreach ($checkoutData['products_to_add'] as $data) {
            $this->productHelper()->frontOpenProduct($data['general_name']);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getParsedMessages());
        $this->clickButton('proceed_to_checkout');
        $this->checkoutOnePageHelper()->frontFillOnePageBillingAddress($checkoutData['billing_address_data']);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        $this->fillCheckbox('gift_option_for_order', 'Yes');
        $this->fillCheckbox('gift_option_for_item', 'Yes');
        //Verification
        $this->assertFalse($this->controlIsVisible('checkbox', 'send_gift_receipt'),
            'Gift Receipt is available');
        $this->assertFalse($this->controlIsVisible('link', 'add_order_gift_message'),
            'Gift Messages on Order Level is available');
        $this->assertTrue($this->controlIsVisible('dropdown', 'order_gift_wrapping_design'),
            'Gift Wrapping on Order Level is not available');
        $this->addParameter('productName', $testData['simple_name']);
        $this->assertFalse($this->controlIsVisible('link', 'add_item_gift_message'),
            'Gift Messages for Order Items is available');
        $this->assertTrue($this->controlIsVisible('dropdown', 'item_gift_wrapping_design'),
            'Gift Wrapping for Order Items is not available');
    }

    /**
     * @TestlinkId TL-MAGE-900
     * <p>Gift Message for Individual Items is allowed</p>
     * <p>Verify that when "Allow Gift Messages for Order Items" setting is set to "Yes",
     * customer has an ability to add Gift Message to Individual Items during OnePageCheckout,
     * prompt for which should be present on Shipping Method step after checking the appropriate checkbox</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftMessageForIndividualItemsIsAllowed($testData)
    {
        //Data
        $items = $this->loadDataSet('OnePageCheckout', 'item_gift_message_with_gift_wrapping', null,
            array('gift_item_product_1' => $testData['simple_name']));
        $checkout = $this->loadDataSet('OnePageCheckout', 'gift_data_general',
            array('add_gift_options' => $items),
            array('add_product_1' => $testData['simple_name'])
        );
        $override = $checkout['shipping_data']['add_gift_options']['individual_items']['item_1']['gift_message'];
        $override['sku_product'] = $testData['simple_sku'];
        $verifyGiftMessage = $this->loadDataSet('OnePageCheckout', 'verify_gift_data', $override);
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_message_per_item_enable');
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkout);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $this->orderHelper()->verifyGiftOptions($verifyGiftMessage);
    }

    /**
     * @TestlinkId TL-MAGE-898
     * @TestlinkId TL-MAGE-910
     * <p>Gift Wrapping image displaying for Entire Order gift options (image is not specified)</p>
     * <p>Gift Wrapping image displaying for Individual Items gift options (image is not specified)</p>
     * <p>Test Case TL-MAGE-842: Enabling Gift Wrapping (OnePageCheckout)</p>
     * <p>Verify that when setting "Allow Gift Wrapping on Order Level" is set to "Yes",
     * customer has ability to select Gift Wrapping (with not specified picture which will not be displayed)
     * for entire Order using dropdown "Gift Wrapping Design" on Payment Method step of OnePageCheckout</p>
     *
     * @param $entity
     * @param $config
     * @param $testData
     *
     * @test
     * @dataProvider giftWrappingImageDisplayingGiftOptionsDataProvider
     * @depends preconditionsForTests
     */
    public function giftWrappingImageDisplayingGiftOptions($entity, $config, $testData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/' . $config);
        //Data
        $byValueParam = ($entity == 'item') ? array('gift_item_product_1' => $testData['simple_name']) : null;
        $options = $this->loadDataSet('OnePageCheckout', $entity . '_gift_wrapping',
            array($entity . '_gift_wrapping_design' => $testData['noImg']['gift_wrapping_design']), $byValueParam);
        $checkout = $this->loadDataSet('OnePageCheckout', 'gift_data_general',
            array('add_gift_options' => $options),
            array('add_product_1' => $testData['simple_name'])
        );
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkout);
        $this->clickControl('link', 'shipping_method_change', false);
        $this->waitForControlVisible('fieldset', 'shipping_method');
        //Verification
        $this->addParameter('itemName', $testData['simple_name']);
        $this->assertTrue($this->controlIsVisible('pageelement', $entity . '_gift_wrapping_price'),
            'There is no price for "Gift Wrapping Design"');
        $this->assertTrue($this->controlIsVisible('pageelement', $entity . '_gift_wrapping_image'),
            'Picture for Gift wrapping is not displayed');
    }

    public function giftWrappingImageDisplayingGiftOptionsDataProvider()
    {
        return array(
            array('item', 'ind_items_gift_wrapping_yes_message_no'),
            array('order', 'order_gift_wrapping_yes_message_no')
        );
    }

    /**
     * @TestlinkId TL-MAGE-924
     * <p>Printed Card is allowed </p>
     * <p>Verify that when "Allow Printed Card" setting is set to "Yes",customer in process of OnePageCheckout
     * have ability to add Printed Card to Order after checking the appropriate checkbox</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function printedCardIsAllowed($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general',
            array('add_gift_options' => $this->loadDataSet('OnePageCheckout', 'gift_message_gift_printed_card')),
            array('add_product_1' => $testData['simple_name']));
        $printedCard = $this->loadDataSet('GiftMessage', 'gift_printed_card_enable');
        $expectedCardPrice = $printedCard['tab_1']['configuration']['gift_options']['default_price_for_printed_card'];
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($printedCard);
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $actualCardPrice = trim($this->getControlAttribute('pageelement', 'printed_card_price', 'text'), '$');
        $this->assertEquals($expectedCardPrice, $actualCardPrice,
            "Printed Card price is different. Actual: $actualCardPrice. Expected:  . $expectedCardPrice");
    }

    /**
     * @TestlinkId TL-MAGE-998
     * <p>Printed Card ia not allowed</p>
     * <p>Verify that when "Allow Printed Card" setting is set to "No",
     * customer is not able to add Printed Card to Order during OnePageCheckout.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function printedCardIsNotAllowed($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('add_product_1' => $testData['simple_name']));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_receipt_enable');
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        foreach ($checkoutData['products_to_add'] as $data) {
            $this->productHelper()->frontOpenProduct($data['general_name']);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getParsedMessages());
        $this->clickButton('proceed_to_checkout');
        $this->checkoutOnePageHelper()->frontFillOnePageBillingAddress($checkoutData['billing_address_data']);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        //Verification
        $this->assertFalse($this->controlIsVisible('checkbox', 'add_printed_card'),
            '"Add Printed card" checkbox is visible');
    }

    /**
     * @TestlinkId TL-MAGE-996
     * <p>Gift Receipt is allowed</p>
     * <p>Verify that when "Allow Gift Receipt" setting is set to "Yes",
     * customer has an ability to enable this option to each order during OnePageCheckout.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftReceiptIsAllowed($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general',
            array('add_gift_options' => $this->loadDataSet('OnePageCheckout', 'gift_message_gift_receipt')),
            array('add_product_1' => $testData['simple_name']));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_receipt_enable');
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $this->orderHelper()->verifyGiftOptions(array('entire_order' => array('order_send_gift_receipt' => 'Yes')));
    }

    /**
     * @TestlinkId TL-MAGE-997
     * <p>Gift Receipt is not allowed</p>
     * <p>Verify that when "Allow Gift Receipt" setting is set to "No",
     * customer is not able to enable this option to each order during OnePageCheckout.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftReceiptIsNotAllowed($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('add_product_1' => $testData['simple_name']));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_printed_card_enable');
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        foreach ($checkoutData['products_to_add'] as $data) {
            $this->productHelper()->frontOpenProduct($data['general_name']);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getParsedMessages());
        $this->clickButton('proceed_to_checkout');
        $this->checkoutOnePageHelper()->frontFillOnePageBillingAddress($checkoutData['billing_address_data']);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        //Verification
        $this->assertFalse($this->controlIsVisible('checkbox', 'send_gift_receipt'),
            '"Send Gift Receipt" checkbox is visible');
    }

    /**
     * @TestlinkId TL-MAGE-912
     * @TestlinkId TL-MAGE-918
     * <p>Recounting Gift Options (Entire Order)</p>
     * <p>Recounting Gift Options (Individual Item)</p>
     * <p>Verify that customer can change configuration of Gift Options more than one time during OnePageCheckout,
     * and Gift Options prices/Grand Total on Order Review step will be recounted according to these changes.</p>
     *
     * @param $entity
     * @param $config
     * @param array $testData
     *
     * @test
     * @dataProvider recountingGiftOptionsDataProvider
     * @depends preconditionsForTests
     */
    public function recountingGiftOptions($entity, $config, $testData)
    {
        if ($entity == 'item') {
            $this->markTestIncomplete('BUG: no gift_wrapping info on order review page for product');
        }
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/' . $config);
        //Data
        $verifyEntity = ($entity == 'item') ? $entity . 's' : $entity;
        $byValueParam = ($entity == 'item') ? array('gift_item_product_1' => $testData['simple_name']) : null;
        //Data for step 1
        $options = $this->loadDataSet('OnePageCheckout', $entity . '_gift_wrapping',
            array($entity . '_gift_wrapping_design' => $testData['noImg']['gift_wrapping_design']), $byValueParam);
        $checkout = $this->loadDataSet('OnePageCheckout', 'recount_gift_wrapping_no_image',
            array(
                'add_gift_options' => $options,
                'gift_wrapping_for_' . $verifyEntity => '$' . $testData['noImg']['gift_wrapping_price']
            ),
            array(
                'add_product_1' => $testData['simple_name'],
                'validate_product_1' => $testData['simple_name']
            )
        );
        //Data for step 2
        $options1 = $this->loadDataSet('OnePageCheckout', $entity . '_gift_wrapping',
            array($entity . '_gift_wrapping_design' => $testData['img']['gift_wrapping_design']), $byValueParam);
        $checkout1 = $this->loadDataSet('OnePageCheckout', 'recount_gift_wrapping_with_image',
            array(
                'add_gift_options' => $options1,
                'gift_wrapping_for_' . $verifyEntity => '$' . $testData['img']['gift_wrapping_price']
            ),
            array(
                'add_product_1' => $testData['simple_name'],
                'validate_product_1' => $testData['simple_name']
            )
        );
        //Data for step 3
        $options2 = $this->loadDataSet('OnePageCheckout', $entity . '_gift_wrapping',
            array($entity . '_gift_wrapping_design' => 'Please select'), $byValueParam);
        $checkout2 = $this->loadDataSet('OnePageCheckout', 'recount_no_gift_wrapping',
            array('add_gift_options' => $options2),
            array('add_product_1' => $testData['simple_name'], 'validate_product_1' => $testData['simple_name'])
        );
        //Steps1
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkout);
        $this->checkoutOnePageHelper()->frontOrderReview($checkout);
        $this->clickControl('link', 'shipping_method_change', false);
        $this->waitForControlVisible('fieldset', 'shipping_method');
        //Steps2
        $this->checkoutOnePageHelper()->frontSelectShippingMethod($checkout1['shipping_data']);
        $this->checkoutOnePageHelper()->frontSelectPaymentMethod($checkout1['payment_data']);
        $this->checkoutOnePageHelper()->frontOrderReview($checkout1);
        $this->clickControl('link', 'shipping_method_change', false);
        $this->waitForControlVisible('fieldset', 'shipping_method');
        //Steps3
        $this->checkoutOnePageHelper()->frontSelectShippingMethod($checkout2['shipping_data']);
        $this->checkoutOnePageHelper()->frontSelectPaymentMethod($checkout2['payment_data']);
        unset($checkout['shipping_data']['add_gift_options']);
        $this->checkoutOnePageHelper()->frontOrderReview($checkout2);
    }


    public function recountingGiftOptionsDataProvider()
    {
        return array(
            array('item', 'ind_items_gift_wrapping_yes_message_no'),
            array('order', 'order_gift_wrapping_yes_message_no')
        );
    }

    /**
     * @TestlinkId TL-MAGE-920
     * <p>Recounting Gift Options (Printed Card to Order)</p>
     * <p>Verify that customer can change Printed Card configuration more than one time during OnePageCheckout,
     * and Printed Card prices/Grand Total on Order Review step are recounted according to these changes.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function recountingGiftOptionsPrintedCard($testData)
    {
        $this->markTestIncomplete('BUG:');
        //Data
        $checkout = $this->loadDataSet('OnePageCheckout', 'recount_gift_wrapping_printed_card_yes', null,
            array('add_product_1' => $testData['simple_name']));
        $checkout2 = $this->loadDataSet('OnePageCheckout', 'recount_gift_wrapping_printed_card_no', null,
            array('add_product_1' => $testData['simple_name']));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_printed_card_enable');
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkout);
        $this->checkoutOnePageHelper()->frontOrderReview($checkout);
        $this->clickControl('link', 'shipping_method_change', false);
        $this->waitForControlVisible('fieldset', 'shipping_method');
        //Steps
        $this->checkoutOnePageHelper()->frontSelectShippingMethod($checkout2['shipping_data']);
        $this->checkoutOnePageHelper()->frontSelectPaymentMethod($checkout2['payment_data']);
        $this->checkoutOnePageHelper()->frontOrderReview($checkout);
    }

    /**
     * @TestlinkId TL-MAGE-1037
     * @TestlinkId TL-MAGE-847
     * <p>No Gift Wrappings is created</p>
     * <p>Disabling Gift Wrapping (OnePageCheckout)</p>
     * <p>Verify that when setting "Allow Gift Wrapping on Order Level" is set to "Yes" and setting
     * "Allow gift wrapping for Order Item" is set to "Yes", dropdown "Gift Wrapping Design" on Payment Method step of
     * checkout should be absent for Entire Order and Individual Item</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function noGiftWrappingsIsCreated($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('add_product_1' => $testData['simple_name']));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_wrapping_for_order_and_per_item_enable');
        //Disabling gift wrapping for TL-MAGE-847
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->disableAllGiftWrapping();
        //Steps
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        foreach ($checkoutData['products_to_add'] as $data) {
            $this->productHelper()->frontOpenProduct($data['general_name']);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getParsedMessages());
        $this->clickButton('proceed_to_checkout');
        $this->checkoutOnePageHelper()->frontFillOnePageBillingAddress($checkoutData['billing_address_data']);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->fillCheckbox('add_gift_options', 'Yes');
        $this->fillCheckbox('gift_option_for_order', 'Yes');
        $this->fillCheckbox('gift_option_for_item', 'Yes');
        //Verification
        $this->assertFalse($this->controlIsVisible('dropdown', 'order_gift_wrapping_design'),
            '"Gift Wrapping Design" is in place');
        $this->addParameter('itemName', $testData['simple_name']);
        $this->assertFalse($this->controlIsPresent('dropdown', 'item_gift_wrapping_design'),
            '"Gift Wrapping Design" is in place');
    }

    /**
     * <p>Preconditions:</p>
     * <p>Create Staging Website</p>
     *
     * @return array $website
     * @test
     * @skipTearDown
     */
    public function preconditionsForTestsPerWebsite()
    {
        $this->markTestIncomplete("Enterprise_Staging is obsolete. The tests should be refactored.");
        //Data
        $website = $this->loadDataSet('StagingWebsite', 'staging_website');
        $product = $this->loadDataSet('Product', 'simple_product_visible',
            array('websites' => $website['general_information']['staging_website_name']));
        $userData = $this->loadDataSet('Customers', 'generic_customer_account',
            array('associate_to_website' => $website['general_information']['staging_website_name']));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('StagingWebsite/staging_website_enable_auto_entries');
        //Steps and Verification
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        $this->assertMessagePresent('success', 'success_created_website');

        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product);
        $this->assertMessagePresent('success', 'success_saved_product');

        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array(
            'site_code' => $website['general_information']['staging_website_code'],
            'site_name' => $website['general_information']['staging_website_name'],
            'simple' => $product['general_name'],
            'user' => array('email' => $userData['email'], 'password' => $userData['password'])
        );
    }

    /**
     * @TestlinkId TL-MAGE-868
     * <p>Test Case: Possibility to adding Gift attributes to Order during the process of OnePageCheckout</p>
     * <p>Check If possible to add Gift Attributes to Order during the process of
     * OnePageCheckout (at Default Website) when all "gift settings" in default scope is set to 'yes" and in the website
     * scope all of this settings set to ""No"</p>
     *
     * @param array $testData
     * @param array $website
     *
     * @test
     * @depends preconditionsForTests
     * @depends preconditionsForTestsPerWebsite
     */
    public function possibilityToAddGiftAttributesToOrder($testData, $website)
    {
        //Data
        $wrappingEnableSite = $this->loadDataSet('GiftMessage', 'gift_wrapping_all_disable_on_website',
            array('configuration_scope' => $website['site_name']));
        $messagesEnableSite = $this->loadDataSet('GiftMessage', 'gift_message_all_disable_on_website',
            array('configuration_scope' => $website['site_name']));
        $wrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $individual = $this->loadDataSet('OnePageCheckout', 'item_gift_message_with_gift_wrapping', null, array(
            'gift_item_product_1' => $testData['simple_name'],
            'item_wrapping' => $wrapping['gift_wrapping_design']
        ));
        $entireOrder = $this->loadDataSet('OnePageCheckout', 'order_gift_message_with_gift_wrapping', null,
            array('order_wrapping' => $wrapping['gift_wrapping_design']));
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general',
            array('entire_order' => $entireOrder['entire_order'], 'individual_items' => $individual),
            array('add_product_1' => $testData['simple_name']));
        $override = array_merge(
            $entireOrder['entire_order']['gift_message'],
            $individual['individual_items']['item_1']['gift_message'],
            array('sku_product' => $testData['simple_sku'])
        );
        $vrfGiftData = $this->loadDataSet('OnePageCheckout', 'verify_gift_data', $override);
        $vrfGiftWrapping = $this->loadDataSet('OnePageCheckout', 'verify_wrapping_data', null, array(
            'order_wrapping' => $wrapping['gift_wrapping_design'],
            'item_wrapping' => $wrapping['gift_wrapping_design'],
            'gift_item_product_1' => $testData['simple_sku'],
            'price_order' => '$' . $wrapping['gift_wrapping_price'],
            'price_product_1' => '$' . $wrapping['gift_wrapping_price']
        ));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_message_all_enable');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_wrapping_all_enable');
        $this->systemConfigurationHelper()->configure($wrappingEnableSite);
        $this->systemConfigurationHelper()->configure($messagesEnableSite);
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($wrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $orderId = $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->loginAdminUser();
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $this->orderHelper()->verifyGiftOptions($vrfGiftData);
        $this->orderHelper()->verifyGiftOptions($vrfGiftWrapping);
    }

    /**
     * @TestlinkId TL-MAGE-857
     * <p>Test Case : Possibility to adding Gift attributes to Order during the process of OnePageCheckout - Website</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTestsPerWebsite
     */
    public function checkoutWithGiftWrappingAndMessageWebsiteScope($testData)
    {
        $this->markTestIncomplete("Enterprise_Staging is obsolete. The tests should be refactored.");
        //Data
        $wrappingEnableSite = $this->loadDataSet('GiftMessage', 'gift_wrapping_all_enable_on_website',
            array('configuration_scope' => $testData['site_name']));
        $messagesEnableSite = $this->loadDataSet('GiftMessage', 'gift_message_all_enable_on_website',
            array('configuration_scope' => $testData['site_name']));
        $wrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image',
            array('gift_wrapping_websites' => $testData['site_name']));
        $items = $this->loadDataSet('OnePageCheckout', 'item_gift_message_with_gift_wrapping', null, array(
            'gift_item_product_1' => $testData['simple'],
            'item_wrapping' => $wrapping['gift_wrapping_design']
        ));
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'gift_data_general',
            array('add_gift_options' => $items),
            array('add_product_1' => $testData['simple'])
        );
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($wrappingEnableSite);
        $this->systemConfigurationHelper()->configure($messagesEnableSite);
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($wrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $newFrontendUrl = $this->stagingWebsiteHelper()->buildFrontendUrl($testData['site_code']);
        $this->getConfigHelper()->setAreaBaseUrl('frontend', $newFrontendUrl);
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * @TestlinkId TL-MAGE-869
     * <p>Test Case: Possibility to adding Gift attributes to Order during the process of OnePageCheckout - Global</p>
     * <p>Verify that it's possible to add Gift Attributes to Order during OnePageCheckout (at Default Website)
     * when all "gift settings" in default scope are set to "Yes"
     * and in the website scope all these settings are set to "No"</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTestsPerWebsite
     */
    public function restrictionToAddGiftAttributesToOrder($testData)
    {
        $this->markTestIncomplete("Enterprise_Staging is obsolete. The tests should be refactored.");
        //Data
        $wrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $checkout = $this->loadDataSet('OnePageCheckout', 'gift_data_general', null,
            array('add_product_1' => $testData['simple']));
        $wrapDisableWebsite = $this->loadDataSet('GiftMessage', 'gift_wrapping_all_disable_on_website',
            array('configuration_scope' => $testData['site_name']));
        $messDisableWebsite = $this->loadDataSet('GiftMessage', 'gift_message_all_disable_on_website',
            array('configuration_scope' => $testData['site_name']));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_message_all_enable');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_wrapping_all_enable');
        $this->systemConfigurationHelper()->configure($wrapDisableWebsite);
        $this->systemConfigurationHelper()->configure($messDisableWebsite);
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($wrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');

        $newFrontendUrl = $this->stagingWebsiteHelper()->buildFrontendUrl($testData['site_code']);
        $this->getConfigHelper()->setAreaBaseUrl('frontend', $newFrontendUrl);
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        foreach ($checkout['products_to_add'] as $data) {
            $this->productHelper()->frontOpenProduct($data['general_name']);
            $this->productHelper()->frontAddProductToCart();
        }
        $this->assertTrue($this->checkCurrentPage('shopping_cart'), $this->getParsedMessages());
        $this->clickButton('proceed_to_checkout');
        $this->checkoutOnePageHelper()->frontFillOnePageBillingAddress($checkout['billing_address_data']);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        //Verification
        $this->assertFalse($this->controlIsVisible('checkbox', 'add_gift_options'),
            '"Add gift options" checkbox is visible');
    }
}