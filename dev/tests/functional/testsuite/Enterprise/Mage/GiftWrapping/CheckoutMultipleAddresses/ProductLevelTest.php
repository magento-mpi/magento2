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
 * Tests for Checkout with Multiple Addresses with gift wrapping and messages.
 * Verifies settings on product level. Frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_GiftWrapping_CheckoutMultipleAddresses_ProductLevelTest extends Mage_Selenium_TestCase
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

    protected function assertPreConditions()
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
     * @test
     * @return array
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $giftWrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        //Steps and Verification
        $simple1 = $this->productHelper()->createSimpleProduct();
        $simple2 = $this->productHelper()->createSimpleProduct();
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');

        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');

        return array(
            'email' => $userData['email'],
            'products' => array($simple1, $simple2),
            'wrapping' => $giftWrapping['gift_wrapping_design']
        );
    }


    /**
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftWrappingAndMessageAvailableForOneItemAndOrder($testData)
    {
        //Data
        list($simple1, $simple2) = $testData['products'];
        $productSettings = $this->loadDataSet('GiftWrapping', 'gift_options_message_yes_wrapping_yes');
        $search = $this->loadDataSet('Product', 'product_search', $simple1['simple']);
        $product1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_order_mess_yes_wrap_yes_item',
            array(
                'product_name' => $simple1['simple']['product_name'],
                'item_gift_wrapping_design' => $testData['wrapping'],
                'order_gift_wrapping_design' => $testData['wrapping']
            )
        );
        $product2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_order',
            array('order_gift_wrapping_design' => $testData['wrapping']));
        $checkout = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['email']),
            array(
                'product_1' => $simple1['simple']['product_name'],
                'product_2' => $simple2['simple']['product_name'],
                'gift_options_address1' => $product1,
                'gift_options_address2' => $product2
            )
        );
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/order_gift_wrapping_yes_message_yes');
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->fillProductTab($productSettings, 'autosettings');
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->frontend();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkout);
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftWrappingAndMessageForItemsOnlyAvailableExceptOneProduct($testData)
    {
        //Data
        list($simple1, $simple2) = $testData['products'];
        $productSettings = $this->loadDataSet('GiftWrapping', 'gift_options_message_no_wrapping_no');
        $search = $this->loadDataSet('Product', 'product_search', $simple1['simple']);
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_item', array(
            'product_name' => $simple2['simple']['product_name'],
            'item_gift_wrapping_design' => $testData['wrapping']
        ));
        $checkout = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['email']),
            array(
                'product_1' => $simple1['simple']['product_name'],
                'product_2' => $simple2['simple']['product_name'],
                'gift_options_address2' => $forProduct2
            )
        );
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_all_yes_order_all_no');
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->fillProductTab($productSettings, 'autosettings');
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->frontend();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkout);
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     *
     * @param string $backendData
     * @param array $testData
     *
     * @test
     * @dataProvider giftWrappingAndMessageForItemAvailableButNotForProductDataProvider
     * @depends preconditionsForTests
     */
    public function giftWrappingAndMessageForItemAvailableButNotForProduct($backendData, $testData)
    {
        if ($backendData == 'ind_items_all_yes_order_wrapping_no_message_yes') {
            $this->markTestIncomplete('BUG: It is impossible to add gift options to order');
        }
        //Data
        list($simple1, $simple2) = $testData['products'];
        $productSettings = $this->loadDataSet('GiftWrapping', 'gift_options_message_no_wrapping_no');
        $search = $this->loadDataSet('Product', 'product_search', $simple1['simple']);

        if ($backendData == 'ind_items_all_yes_order_wrapping_yes_message_no') {
            $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order',
                array('order_gift_wrapping_design' => $testData['wrapping']));
            $forProduct2 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order_mess_yes_wrap_yes_item', array(
                    'product_name' => $simple2['simple']['product_name'],
                    'item_gift_wrapping_design' => $testData['wrapping'],
                    'order_gift_wrapping_design' => $testData['wrapping']
                ));
        } else {
            $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order');
            $forProduct2 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order_mess_yes_wrap_yes_item', array(
                    'product_name' => $simple2['simple']['product_name'],
                    'item_gift_wrapping_design' => $testData['wrapping']
                ));
        }
        $checkout = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['email']),
            array(
                'product_1' => $simple1['simple']['product_name'],
                'product_2' => $simple2['simple']['product_name'],
                'gift_options_address1' => $forProduct1,
                'gift_options_address2' => $forProduct2
            )
        );
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/' . $backendData);
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->fillProductTab($productSettings, 'autosettings');
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->frontend();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkout);
        $this->assertMessagePresent('success', 'success_checkout');
    }

    public function giftWrappingAndMessageForItemAvailableButNotForProductDataProvider()
    {
        return array(
            array('ind_items_all_yes_order_wrapping_yes_message_no'),
            array('ind_items_all_yes_order_wrapping_no_message_yes')
        );
    }

    /**
     * @param string $backendData
     * @param array $testData
     *
     * @test
     * @dataProvider giftWrappingAndMessageForItemAvailableButNotForProductDataProvider
     * @depends preconditionsForTests
     */
    public function giftWrappingAndMessageForItemAvailableButGiftWrappingForProductIsNot($backendData, $testData)
    {
        $this->markTestIncomplete('BUG: It is impossible to add gift options to order');
        //Data
        list($simple1, $simple2) = $testData['products'];
        $productSettings = $this->loadDataSet('GiftWrapping', 'gift_options_message_yes_wrapping_no');
        $search = $this->loadDataSet('Product', 'product_search', $simple1['simple']);
        if ($backendData == 'ind_items_all_yes_order_wrapping_yes_message_no') {
            $forProduct1 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order_mess_yes_wrap_no_item', array(
                    'product_name' => $simple1['simple']['product_name'],
                    'order_gift_wrapping_design' => $testData['wrapping']
                ));
            $forProduct2 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order_mess_yes_wrap_yes_item', array(
                    'product_name' => $simple2['simple']['product_name'],
                    'item_gift_wrapping_design' => $testData['wrapping'],
                    'order_gift_wrapping_design' => $testData['wrapping']
                ));
        } else {
            $forProduct1 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order_mess_yes_wrap_no_item',
                    array('product_name' => $simple1['simple']['product_name']));
            $forProduct2 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order_mess_yes_wrap_yes_item', array(
                    'product_name' => $simple2['simple']['product_name'],
                    'item_gift_wrapping_design' => $testData['wrapping']
                ));
        }
        $checkout = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['email']),
            array(
                'product_1' => $simple1['simple']['product_name'],
                'product_2' => $simple2['simple']['product_name'],
                'gift_options_address1' => $forProduct1,
                'gift_options_address2' => $forProduct2
            )
        );
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/' . $backendData);
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->fillProductTab($productSettings, 'autosettings');
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->frontend();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkout);
        $this->assertMessagePresent('success', 'success_checkout');
    }


    /**
     * @param string $backendData
     * @param array $testData
     *
     * @test
     * @dataProvider giftWrappingAndMessageForItemAvailableButNotForProductDataProvider
     * @depends preconditionsForTests
     */
    public function giftWrappingAndMessageForItemAvailableButGiftMessageForProductIsNot($backendData, $testData)
    {
        $this->markTestIncomplete('BUG: It is impossible to add gift options to order');
        //Data
        list($simple1, $simple2) = $testData['products'];
        $productSettings = $this->loadDataSet('GiftWrapping', 'gift_options_message_no_wrapping_yes');
        $search = $this->loadDataSet('Product', 'product_search', $simple1['simple']);

        if ($backendData == 'ind_items_all_yes_order_wrapping_yes_message_no') {
            $forProduct1 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order_mess_no_wrap_yes_item', array(
                    'product_name' => $simple1['simple']['product_name'],
                    'item_gift_wrapping_design' => $testData['wrapping'],
                    'order_gift_wrapping_design' => $testData['wrapping']
                ));
            $forProduct2 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order_mess_yes_wrap_yes_item', array(
                    'product_name' => $simple2['simple']['product_name'],
                    'item_gift_wrapping_design' => $testData['wrapping'],
                    'order_gift_wrapping_design' => $testData['wrapping']
                ));
        } else {
            $forProduct1 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order_mess_no_wrap_yes_item', array(
                    'product_name' => $simple1['simple']['product_name'],
                    'item_gift_wrapping_design' => $testData['wrapping']
                ));
            $forProduct2 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order_mess_yes_wrap_yes_item', array(
                    'product_name' => $simple2['simple']['product_name'],
                    'item_gift_wrapping_design' => $testData['wrapping']
                ));
        }
        $checkout = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['email']),
            array(
                'product_1' => $simple1['simple']['product_name'],
                'product_2' => $simple2['simple']['product_name'],
                'gift_options_address1' => $forProduct1,
                'gift_options_address2' => $forProduct2
            )
        );
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/' . $backendData);
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->fillProductTab($productSettings, 'autosettings');
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->frontend();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkout);
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftWrappingForItemAndOrderCustomPriceForProduct($testData)
    {
        //Data
        list($simple1, $simple2) = $testData['products'];
        $productSettings = $this->loadDataSet('GiftWrapping', 'gift_options_use_default',
            array('autosettings_price_for_gift_wrapping' => '1.23'));
        $search = $this->loadDataSet('Product', 'product_search', $simple1['simple']);
        $forProduct1 =
            $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_order_mess_yes_wrap_yes_item', array(
                'product_name' => $simple1['simple']['product_name'],
                'item_gift_wrapping_design' => $testData['wrapping'],
                'order_gift_wrapping_design' => $testData['wrapping']
            ));
        $forProduct2 =
            $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_order_mess_yes_wrap_yes_item', array(
                'product_name' => $simple2['simple']['product_name'],
                'item_gift_wrapping_design' => $testData['wrapping'],
                'order_gift_wrapping_design' => $testData['wrapping']
            ));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['email']),
            array(
                'product_1' => $simple1['simple']['product_name'],
                'product_2' => $simple2['simple']['product_name'],
                'gift_options_address1' => $forProduct1,
                'gift_options_address2' => $forProduct2
            )
        );
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/ind_items_all_yes_order_all_yes');
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->fillProductTab($productSettings, 'autosettings');
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->frontend();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
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
        //Creating a website
        $website = $this->loadDataSet('StagingWebsite', 'staging_website');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('StagingWebsite/staging_website_enable_auto_entries');
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        $this->assertMessagePresent('success', 'success_created_website');
        //Creating a gift wrapping for the website
        $giftWrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $giftWrapping['gift_wrapping_websites'] .= ',' . $website['general_information']['staging_website_name'];
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        //Creating products for the website
        $product1 = $this->loadDataSet('Product', 'simple_product_visible');
        $product1['websites'] .= ',' . $website['general_information']['staging_website_name'];
        $product2 = $this->loadDataSet('Product', 'simple_product_visible');
        $product2['websites'] .= ',' . $website['general_information']['staging_website_name'];
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($product1);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($product2);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Creating a customer
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $newFrontendUrl = $this->stagingWebsiteHelper()->buildFrontendUrl(
            $website['general_information']['staging_website_code']);
        $this->getConfigHelper()->setAreaBaseUrl('frontend', $newFrontendUrl);
        $this->logoutCustomer();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        return array(
            'website' => $website['general_information'],
            'email' => $userData['email'],
            'products' => array($product1['general_name'], $product2['general_name']),
            'wrapping' => $giftWrapping
        );
    }

    /**
     * @param array $testData
     *
     * @test
     *
     * @depends preconditionsForTestsWithWebSite
     * @TestlinkId TL-MAGE-1044*
     */
    public function giftWrappingPriceOnProductLevelForStoreView($testData)
    {
        $this->markTestIncomplete("Enterprise_Staging is obsolete. The tests should be refactored.");
        //Data
        $productGiftSettings = $this->loadDataSet('GiftWrapping', 'gift_options_custom_wrapping_price');
        $productGSOnSView = $this->loadDataSet('GiftWrapping',
            'gift_options_custom_wrapping_price_on_store_view');
        $this->assertNotEquals($productGiftSettings['gift_options_price_for_gift_wrapping'],
            $productGSOnSView['gift_options_price_for_gift_wrapping']);
        $this->assertNotEquals($productGiftSettings['gift_options_price_for_gift_wrapping'],
            $testData['wrapping']['gift_wrapping_price']);
        $website = $testData['website'];
        $product1 = $testData['products'][0]; // This product will have custom gift option settings
        $product2 = $testData['products'][1];
        $giftWrappingName = $testData['wrapping']['gift_wrapping_design'];
        $forProduct1 =
            $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order_mess_no_wrap_yes_item', array(
                'product_name' => $product1,
                'item_gift_wrapping_design' => $giftWrappingName,
                'order_gift_wrapping_design' => $giftWrappingName
            ));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email' => $testData['email'], 'product_qty' => 1),
            array(
                'product_1' => $product1,
                'product_2' => $product2,
                'gift_options_address1' => $forProduct1,
                'gift_options_address2' => '%noValue%'
            )
        );
        $verifyPrices =
            $this->loadDataSet('MultipleAddressesCheckout', 'verify_prices_gift_wrapping_TL-MAGE-1044', null, array(
                'product1' => $product1, 'product2' => $product2,
                'product1wrapping' => $giftWrappingName
            ));
        $checkoutData['verify_prices'] = $verifyPrices;
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_wrapping_for_order_and_per_item_enable');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_website_price_scope');
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_name' => $product1));
        $this->selectStoreScope('dropdown', 'choose_store_view', 'Default Values');
        $this->acceptAlert();
        $this->productHelper()->fillTab($productGiftSettings, 'autosettings');
        $this->clickButton('save_and_continue_edit');
        $this->assertMessagePresent('success', 'success_saved_product');
        $storeView = $website['staging_website_name'] . '/Main Website Store/Default Store View';
        $this->selectStoreScope('dropdown', 'choose_store_view', $storeView);
        $this->acceptAlert();
        $this->productHelper()->fillTab($productGSOnSView, 'autosettings');
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $newFrontendUrl = $this->stagingWebsiteHelper()->buildFrontendUrl($website['staging_website_code']);
        $this->getConfigHelper()->setAreaBaseUrl('frontend', $newFrontendUrl);
        $orderId = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderId) == 2, 'Expected that exactly 2 orders have been created.');
    }
}