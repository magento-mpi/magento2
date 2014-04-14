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
    }

    public function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_disable_all');
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @return array $productData
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $wrapping1 = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $wrapping2 = $this->loadDataSet('GiftWrapping', 'gift_wrapping_with_image');
        //Steps and Verification
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');

        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($wrapping1);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($wrapping2);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');

        $this->frontend();
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');

        return array(
            'img' => array(
                'design' => $wrapping2['gift_wrapping_design'],
                'price' => '$' . $wrapping2['gift_wrapping_price']
            ),
            'noImg' => array(
                'design' => $wrapping1['gift_wrapping_design'],
                'price' => '$' . $wrapping1['gift_wrapping_price']
            ),
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
//        @TODO BUG
//        $this->assertFalse($this->controlIsVisible('checkbox', 'gift_option_for_order'),
//            'It is possible to add gift options for the Entire Order');
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
            array($entity . '_gift_wrapping_design' => $testData['noImg']['design']), $byValueParam);
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
     * @TestlinkId TL-MAGE-912
     * <p>Recounting Gift Options for Entire Order</p>
     * <p>Verify that customer can change configuration of Gift Options more than one time during OnePageCheckout,
     * and Gift Options prices/Grand Total on Order Review step will be recounted according to these changes.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function recountingGiftOptionsForEntireOrder($testData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/order_gift_wrapping_yes_message_no');
        //Data
        //Data for step 1
        $checkout = $this->loadDataSet('OnePageCheckout', 'recount_gift_wrapping_no_image',
            array(
                'individual_items' => '%noValue%',
                'gift_message' => '%noValue%',
                'gift_wrapping_for_order' => $testData['noImg']['price']
            ),
            array('order_wrapping' => $testData['noImg']['design'], 'add_product_1' => $testData['simple_name'])
        );
        //Data for step 2
        $checkout1 = $this->loadDataSet('OnePageCheckout', 'recount_gift_wrapping_with_image',
            array(
                'individual_items' => '%noValue%',
                'gift_message' => '%noValue%',
                'gift_wrapping_for_order' => $testData['img']['price']
            ),
            array('order_wrapping' => $testData['img']['design'], 'add_product_1' => $testData['simple_name'])
        );
        //Data for step 3
        $options = $this->loadDataSet(
            'OnePageCheckout', 'order_gift_wrapping', array('order_gift_wrapping_design' => 'Please select')
        );
        $checkout2 = $this->loadDataSet('OnePageCheckout', 'recount_no_gift_wrapping',
            array('add_gift_options' => $options),
            array('add_product_1' => $testData['simple_name'])
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

    /**
     * @TestlinkId TL-MAGE-918
     * <p>Recounting Gift Options for Individual Item</p>
     * <p>Verify that customer can change configuration of Gift Options more than one time during OnePageCheckout,
     * and Gift Options prices/Grand Total on Order Review step will be recounted according to these changes.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function recountingGiftOptionsForIndividualItem($testData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_gift_wrapping_yes_message_no');
        //Data
        //Data for step 1
        $checkout = $this->loadDataSet('OnePageCheckout', 'recount_gift_wrapping_no_image',
            array(
                'entire_order' => '%noValue%',
                'gift_message' => '%noValue%',
                'gift_wrapping_for_items' => $testData['noImg']['price']
            ),
            array(
                'product1wrapping' => $testData['noImg']['design'],
                'add_product_1' => $testData['simple_name'],
                'gift_item_product_1' => $testData['simple_name']
            )
        );
        //Data for step 2
        $checkout1 = $this->loadDataSet('OnePageCheckout', 'recount_gift_wrapping_with_image',
            array(
                'entire_order' => '%noValue%',
                'gift_message' => '%noValue%',
                'gift_wrapping_for_items' => $testData['img']['price']
            ),
            array(
                'product1wrapping' => $testData['img']['design'],
                'add_product_1' => $testData['simple_name'],
                'gift_item_product_1' => $testData['simple_name']
            )
        );
        //Data for step 3
        $options = $this->loadDataSet('OnePageCheckout', 'item_gift_wrapping', null, array(
            'gift_item_product_1' => $testData['simple_name'],
            'product1wrapping' => 'Please select'
        ));
        $checkout2 = $this->loadDataSet('OnePageCheckout', 'recount_no_gift_wrapping',
            array('add_gift_options' => $options),
            array('add_product_1' => $testData['simple_name'])
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
        $this->markTestIncomplete('BUG: Printed Card price present on order review after unselecting');
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
        $this->checkoutOnePageHelper()->frontOrderReview($checkout2);
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
}