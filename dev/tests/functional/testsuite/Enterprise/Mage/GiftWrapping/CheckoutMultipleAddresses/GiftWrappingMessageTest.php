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
 * Tests for Checkout with Multiple Addresses with gift wrapping and messages. Frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_GiftWrapping_CheckoutMultipleAddresses_GiftWrappingMessageTest extends Mage_Selenium_TestCase
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
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        $product1 = $this->loadDataSet('Product', 'simple_product_visible');
        $product2 = $this->loadDataSet('Product', 'simple_product_visible');
        $userDefault = $this->loadDataSet('Customers', 'generic_customer_account');
        $wrappingDefault = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        //Steps and Verification
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product1);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($product2);
        $this->assertMessagePresent('success', 'success_saved_product');

        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userDefault);
        $this->assertMessagePresent('success', 'success_saved_customer');

        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($wrappingDefault);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');

        return array(
            'wrapping' => $wrappingDefault['gift_wrapping_design'],
            'simple1' => $product1['general_name'],
            'simple2' => $product2['general_name'],
            'user' => $userDefault['email'],
        );
    }

    /**
     * <p>Test cases: TL-MAGE-965 and TL-MAGE-961 and TL-MAGE-853 and TL-MAGE-871</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function withGiftWrappingAndMessage($testData)
    {
        $this->markTestIncomplete('BUG: no gift_wrapping info on order review page for product');
        //Data
        $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'all_variants_for_gift_options', array(
            'product_name' => $testData['simple1'],
            'item_gift_wrapping_design' => $testData['wrapping'],
            'order_gift_wrapping_design' => $testData['wrapping']
        ));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'all_variants_for_gift_options', array(
            'product_name' => $testData['simple2'],
            'item_gift_wrapping_design' => $testData['wrapping'],
            'order_gift_wrapping_design' => $testData['wrapping']
        ));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['user']),
            array(
                'product_1' => $testData['simple1'],
                'product_2' => $testData['simple2'],
                'gift_options_address1' => $forProduct1,
                'gift_options_address2' => $forProduct2
            )
        );
        $verifyPrices = $this->loadDataSet('MultipleAddressesCheckout', 'verify_prices_all_gift_options', null, array(
            'product1' => $testData['simple1'],
            'product2' => $testData['simple2'],
            'product1wrapping' => $testData['wrapping'],
            'product2wrapping' => $testData['wrapping']
        ));
        $checkoutData['verify_prices'] = $verifyPrices;
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_enable_all');
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Test case:</p>
     *
     * <p>Expected Results:</p>
     * <p>1. "Add Gift Options for Entire Order" and "Gift Message" for entire order only are present;</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftWrappingAndMessagesForEntireOrderOnly($testData)
    {
        //Data
        $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_order',
            array('order_gift_wrapping_design' => $testData['wrapping']));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_order',
            array('order_gift_wrapping_design' => $testData['wrapping']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['user']),
            array(
                'product_1' => $testData['simple1'],
                'product_2' => $testData['simple2'],
                'gift_options_address1' => $forProduct1,
                'gift_options_address2' => $forProduct2
            )
        );
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/order_gift_wrapping_yes_message_yes');
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>TL-MAGE-970, TL-MAGE-962: Gift Wrapping for entire Order is not allowed (wrapping-no; message-no)</p>
     *
     * <p>Expected Results:</p>
     * <p>1. "Add Gift Options for Individual Items" and "Gift Message" for individual items only are present;</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftWrappingAndMessagesForIndItemsOnly($testData)
    {
        //Data
        $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_item', array(
            'product_name' => $testData['simple1'],
            'item_gift_wrapping_design' => $testData['wrapping']
        ));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_item', array(
            'product_name' => $testData['simple2'],
            'item_gift_wrapping_design' => $testData['wrapping']
        ));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['user']),
            array(
                'product_1' => $testData['simple1'],
                'product_2' => $testData['simple2'],
                'gift_options_address1' => $forProduct1,
                'gift_options_address2' => $forProduct2
            )
        );
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_all_yes_order_all_no');
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>TL-MAGE-973:Gift Wrapping for Individual Items is not allowed (wrapping-no; message-yes)</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftWrappingForIndItemsNoAndMessagesYes($testData)
    {
        $this->markTestIncomplete('BUG: It is impossible to add gift options to order');
        //Data
        $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_item',
            array('product_name' => $testData['simple1']));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_item',
            array('product_name' => $testData['simple2']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['user']),
            array(
                'product_1' => $testData['simple1'],
                'product_2' => $testData['simple2'],
                'gift_options_address1' => $forProduct1,
                'gift_options_address2' => $forProduct2
            )
        );
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_gift_wrapping_no_message_yes');
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>TL-MAGE-967:Gift Message for Individual Items is not allowed (message-no; wrapping-yes)</p>
     *
     * <p>Expected Results:</p>
     * <p>1. "Add gift options for Individual Items" checkbox should not be shown.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftWrappingForIndItemsYesAndMessagesNo($testData)
    {
        //Data
        $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_item',
            array('product_name' => $testData['simple1'], 'item_gift_wrapping_design' => $testData['wrapping']));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_item',
            array('product_name' => $testData['simple2'], 'item_gift_wrapping_design' => $testData['wrapping']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['user']),
            array(
                'product_1' => $testData['simple1'],
                'product_2' => $testData['simple2'],
                'gift_options_address1' => $forProduct1,
                'gift_options_address2' => $forProduct2
            )
        );
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_gift_wrapping_yes_message_no');
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>TL-MAGE-963:Gift Wrapping for entire Order is not allowed (wrapping-no; message-yes)</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftWrappingForOrderNoAndMessagesYes($testData)
    {
        $this->markTestIncomplete('BUG: It is impossible to add gift options to order');
        //Data
        $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order');
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order');
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['user']),
            array(
                'product_1' => $testData['simple1'],
                'product_2' => $testData['simple2'],
                'gift_options_address1' => $forProduct1,
                'gift_options_address2' => $forProduct2
            )
        );
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/order_gift_wrapping_no_message_yes');
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Test Case TL-MAGE-960: Gift Message for entire Order is not allowed (message-no; wrapping-yes)</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftWrappingForOrderYesAndMessagesNo($testData)
    {
        //Data
        $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order',
            array('order_gift_wrapping_design' => $testData['wrapping']));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order',
            array('order_gift_wrapping_design' => $testData['wrapping']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['user']),
            array(
                'product_1' => $testData['simple1'],
                'product_2' => $testData['simple2'],
                'gift_options_address1' => $forProduct1,
                'gift_options_address2' => $forProduct2
            )
        );
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/order_gift_wrapping_yes_message_no');
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Test Case TL-MAGE-992: Printed Card to Order is allowed</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function withGiftCard($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['user']),
            array(
                'product_1' => $testData['simple1'],
                'product_2' => $testData['simple2'],
                'gift_options_address1' => array('add_printed_card' => 'Yes'),
                'gift_options_address2' => array('add_printed_card' => 'Yes')
            )
        );
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_printed_card_enable');
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Test Case TL-MAGE-994: Gift Receipt is allowed</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftReceiptYes($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['user']),
            array(
                'product_1' => $testData['simple1'],
                'product_2' => $testData['simple2'],
                'gift_options_address1' => array('send_gift_receipt' => 'Yes'),
                'gift_options_address2' => array('send_gift_receipt' => 'Yes')
            )
        );
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_receipt_enable');
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Test Case TL-MAGE-937: Recounting Gift Options (Entire Order)</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function recountingGiftWrappingForOrder($testData)
    {
        $this->markTestIncomplete('BUG: It is impossible to add gift options to order');
        //Data
        $product1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order');
        $product2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order');
        $reconfigureProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order');
        $reconfigureProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order');
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['user']),
            array(
                'product_1' => $testData['simple1'],
                'product_2' => $testData['simple2'],
                'gift_options_address1' => $product1,
                'gift_options_address2' => $product2
            )
        );
        $reconfigureShipping =
            $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login/shipping_data', null, array(
                'product_1' => $testData['simple1'],
                'product_2' => $testData['simple2'],
                'gift_options_address1' => $reconfigureProduct1,
                'gift_options_address2' => $reconfigureProduct2
            ));
        $reconfiguredCheckout = $checkoutData;
        $reconfiguredCheckout['shipping_data'] = $reconfigureShipping;
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/order_gift_wrapping_no_message_yes');
        $this->checkoutMultipleAddressesHelper()->doMultipleCheckoutSteps($checkoutData);
        $this->clickControl('link', 'shipping_method_change');
        $this->checkoutMultipleAddressesHelper()->verifyGiftOptions($checkoutData['shipping_data']);
        $this->checkoutMultipleAddressesHelper()->defineAndSelectShippingMethods($reconfigureShipping);
        $this->clickButton('continue_to_review_order');
        $this->checkoutMultipleAddressesHelper()->frontOrderReview($reconfiguredCheckout);
        $this->checkoutMultipleAddressesHelper()->submitMultipleCheckoutSteps();
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Test Case TL-MAGE-942: Recounting Gift Options (Individual Item)</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function recountingGiftWrappingForItems($testData)
    {
        $wrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        //Data
        $product1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_item',
            array('product_name' => $testData['simple1'], 'item_gift_wrapping_design' => $testData['wrapping']));
        $product2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_item',
            array('product_name' => $testData['simple2'], 'item_gift_wrapping_design' => $testData['wrapping']));
        $reconfigureProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_item', array(
            'product_name' => $testData['simple1'],
            'item_gift_wrapping_design' => $wrapping['gift_wrapping_design']
        ));
        $reconfigureProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_item', array(
            'product_name' => $testData['simple2'],
            'item_gift_wrapping_design' => $wrapping['gift_wrapping_design']
        ));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['user']),
            array(
                'product_1' => $testData['simple1'],
                'product_2' => $testData['simple2'],
                'gift_options_address1' => $product1,
                'gift_options_address2' => $product2
            )
        );
        $reconfigureShipping =
            $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login/shipping_data', null, array(
                'product_1' => $testData['simple1'],
                'product_2' => $testData['simple2'],
                'gift_options_address1' => $reconfigureProduct1,
                'gift_options_address2' => $reconfigureProduct2
            ));
        $reconfiguredCheckout = $checkoutData;
        $reconfiguredCheckout['shipping_data'] = $reconfigureShipping;
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_all_yes_order_all_no');
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($wrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->checkoutMultipleAddressesHelper()->doMultipleCheckoutSteps($checkoutData);
        $this->clickControl('link', 'shipping_method_change');
        $this->checkoutMultipleAddressesHelper()->verifyGiftOptions($checkoutData['shipping_data']);
        $this->checkoutMultipleAddressesHelper()->defineAndSelectShippingMethods($reconfigureShipping);
        $this->clickButton('continue_to_review_order');
        $this->checkoutMultipleAddressesHelper()->frontOrderReview($reconfiguredCheckout);
        $this->checkoutMultipleAddressesHelper()->submitMultipleCheckoutSteps();
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');

    }

    /**
     * <p>Test Case TL-MAGE-943: Recounting Gift Options (Printed Card to Order)</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function recountingGiftCard($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['user']),
            array('product_1' => $testData['simple1'], 'product_2' => $testData['simple2']));
        $reconfigureShipping =
            $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login/shipping_data', null, array(
                'product_1' => $testData['simple1'],
                'product_2' => $testData['simple2'],
                'gift_options_address1' => array('add_printed_card' => 'Yes'),
                'gift_options_address2' => array('add_printed_card' => 'Yes')
            ));
        $reconfiguredCheckout = $checkoutData;
        $reconfiguredCheckout['shipping_data'] = $reconfigureShipping;
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_printed_card_enable');
        $this->checkoutMultipleAddressesHelper()->doMultipleCheckoutSteps($checkoutData);
        $this->clickControl('link', 'shipping_method_change');
        $this->checkoutMultipleAddressesHelper()->verifyGiftOptions($checkoutData['shipping_data']);
        $this->checkoutMultipleAddressesHelper()->defineAndSelectShippingMethods($reconfigureShipping);
        $this->clickButton('continue_to_review_order');
        $this->checkoutMultipleAddressesHelper()->frontOrderReview($reconfiguredCheckout);
        $this->checkoutMultipleAddressesHelper()->submitMultipleCheckoutSteps();
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');

    }

    /**
     * <p>Test Case TL-MAGE-1039: No Gift Wrappings is created</p>
     * <p>Test Case TL-MAGE-858: Disabling Gift Wrapping (Multiple Addresses Checkout)</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function withoutActiveGiftWrapping($testData)
    {
        //Data
        $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order_mess_yes_wrap_no_item',
            array('product_name' => $testData['simple1']));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order_mess_yes_wrap_no_item',
            array('product_name' => $testData['simple2']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['user']),
            array(
                'product_1' => $testData['simple1'],
                'product_2' => $testData['simple2'],
                'gift_options_address1' => $forProduct1,
                'gift_options_address2' => $forProduct2
            ));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_all_yes_order_all_yes');
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->disableAllGiftWrapping();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * @test
     * @return array
     * @skipTearDown
     */
    public function preconditionsForTestsWithWebSite()
    {
        $this->markTestIncomplete("Enterprise_Staging is obsolete. The tests should be refactored.");
        //Data
        $website = $this->loadDataSet('StagingWebsite', 'staging_website');
        $websiteName = $website['general_information']['staging_website_name'];
        $product = $this->loadDataSet('Product', 'simple_product_visible', array('websites' => $websiteName));
        $user = $this->loadDataSet('Customers', 'generic_customer_account',
            array('associate_to_website' => $websiteName));
        $wrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image',
            array('gift_wrapping_websites' => $websiteName));
        //Steps and Verification
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('StagingWebsite/staging_website_enable_auto_entries');

        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        $this->assertMessagePresent('success', 'success_created_website');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($user);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($wrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        return array(
            'simple' => $product['general_name'],
            'user' => $user['email'],
            'wrapping' => $wrapping['gift_wrapping_design'],
            'website' => $websiteName,
            'code' => $website['general_information']['staging_website_code']
        );
    }

    /**
     * <p>Test Case TL-MAGE-860: Possibility to adding Gift attributes to Order during the process of Multiple Addresses
     * Checkout - Website</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTestsWithWebSite
     */
    public function withGiftWrappingAndMessagePerWebsiteScope($testData)
    {
        $this->markTestIncomplete("Enterprise_Staging is obsolete. The tests should be refactored.");
        //Data
        $wrappingWebsite = $this->loadDataSet('GiftMessage', 'gift_wrapping_all_enable_on_website',
            array('configuration_scope' => $testData['website']));
        $giftMessagesWebsite = $this->loadDataSet('GiftMessage', 'gift_message_all_enable_on_website',
            array('configuration_scope' => $testData['website']));
        $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'all_variants_for_gift_options', array(
            'product_name' => $testData['simple'], 'item_gift_wrapping_design' => $testData['wrapping'],
            'order_gift_wrapping_design' => $testData['wrapping']
        ));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'all_variants_for_gift_options', array(
            'product_name' => $testData['simple'],
            'item_gift_wrapping_design' => $testData['wrapping'],
            'order_gift_wrapping_design' => $testData['wrapping']
        ));
        $productsAdd = $this->loadDataSet('MultipleAddressesCheckout', 'products_to_add_with_qty', null,
            array('product_1' => $testData['simple'], 'product_2' => $testData['simple']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['user'], 'products_to_add' => $productsAdd),
            array(
                'product_1' => $testData['simple'],
                'product_2' => $testData['simple'],
                'gift_options_address1' => $forProduct1,
                'gift_options_address2' => $forProduct2
            )
        );
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($wrappingWebsite);
        $this->systemConfigurationHelper()->configure($giftMessagesWebsite);
        $newFrontendUrl = $this->stagingWebsiteHelper()->buildFrontendUrl($testData['code']);
        $this->getConfigHelper()->setAreaBaseUrl('frontend', $newFrontendUrl);
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }
}
