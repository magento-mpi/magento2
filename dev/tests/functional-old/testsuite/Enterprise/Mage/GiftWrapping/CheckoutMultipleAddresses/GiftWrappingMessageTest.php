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
    }

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_disable_all');
    }

    /**
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        $product1 = $this->loadDataSet('Product', 'simple_product_visible');
        $product2 = $this->loadDataSet('Product', 'simple_product_visible');
        $userDefault = $this->loadDataSet('Customers', 'customer_account_register');
        $wrappingDefault = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        //Steps and Verification
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product1);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($product2);
        $this->assertMessagePresent('success', 'success_saved_product');

        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($wrappingDefault);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');

        $this->frontend();
        $this->customerHelper()->registerCustomer($userDefault);
        $this->assertMessagePresent('success', 'success_registration');

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
}
