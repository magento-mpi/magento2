<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
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
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_disable_all');
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
        $this->_configHelper->getConfigAreas(true);
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('GiftMessage/gift_options_disable_all');
    }

    /**
     * @test
     * @return array
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $giftWrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        //Steps and Verification
        $simple1 = $this->productHelper()->createSimpleProduct();
        $simple2 = $this->productHelper()->createSimpleProduct();
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array('email'    => $userData['email'],
                     'products' => array($simple1, $simple2),
                     'wrapping' => $giftWrapping['gift_wrapping_design']);
    }

    /**
     * @test
     * @return array
     */
    public function preconditionsForTestsWithWebSite()
    {
        //Creating a website
        $website = $this->loadDataSet('StagingWebsite', 'staging_website');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('staging_website_enable_auto_entries');
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
        $this->_configHelper->setAreaBaseUrl('frontend', $newFrontendUrl);
        $this->logoutCustomer();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');
        return array('website' => $website['general_information'],
                     'email'    => $userData['email'],
                     'products' => array($product1['general_name'], $product2['general_name']),
                     'wrapping' => $giftWrapping);
    }

    /**
     * <p>Preconditions:</p>
     * <ol>
     * <li>Simple product is created;</li>
     * <li>Customer is created;</li>
     * <li>Gift wrapping is created;</li>
     * <li>Gift wrapping and messages On Item Level are set to "No" in system configuration.</li>
     * <li>Gift wrapping and messages On Order Level are set to "Yes" in system configuration.</li>
     * <li>Gift wrapping and messages are set to "Yes" in product settings for product#1 only.</li>
     * </ol>
     * <p>Steps:</p>
     * <ol>
     * <li>Log into Frontend;</li>
     * <li>Add one product to shopping cart;</li>
     * <li>Click "Checkout with Multiple Addresses" link;</li>
     * <li>Select different shipping addresses for items;</li>
     * <li>Click button "Continue to shipping information";</li>
     * <li>Select "Flat Rate" shipping method for item;</li>
     * <li>Check "Add gift options" checkbox;</li>
     * <li>Check "Add Gift Options for Entire Order" checkbox;</li>
     * <li>Select Gift Wrapping from "Gift Wrapping Design" dropdown;</li>
     * <li>Click "Gift Message" link for entire order;</li>
     * <li>Add gift message for entire order;</li>
     * <li>Check "Add gift options for Individual Items" checkbox in the second item.</li>
     * <li>Select Gift Wrapping from "Gift Wrapping Design" dropdown for item;</li>
     * <li>Click "Gift Message" link for individual item;</li>
     * <li>Add gift message for individual item;</li>
     * <p>Expected Results:</p>
     * <ol><li>
     * For product#1 with settings on product level:
     * "Add Gift Options for Individual Items" checkbox is present;
     * "Gift Wrapping" for Individual Items is present;
     * "Gift Message" for Individual Items is present;
     * "Add Gift Options for the Entire Order" checkbox is present;
     * "Gift Wrapping" for Entire Order is present;
     * "Gift Message" for Entire Order  is present;
     * </li>
     * <li>
     * For product#2 without settings on product level:
     * "Add Gift Options for the Entire Order" checkbox is present;
     * "Gift Wrapping" for Entire Order is present:
     * "Gift Message" for Entire Order is present;
     * "Add Gift Options for Individual Items" checkbox isn't present;
     * </li></ol>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftWrappingAndMessageAvailableForOneItemAndOrder($testData)
    {
        //Data
        list($simple1, $simple2) = $testData['products'];
        $backendSettings = $this->loadDataSet('GiftMessage', 'ind_items_gift_wrapping_no_message_no');
        $productSettings = $this->loadDataSet('GiftWrapping', 'gift_options_message_yes_wrapping_yes');
        $search = $this->loadDataSet('Product', 'product_search', $simple1['simple']);
        $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_order_mess_yes_wrap_yes_item',
            array('product_name'            => $simple1['simple']['product_name'],
                  'gift_wrapping_for_item'  => $testData['wrapping'],
                  'gift_wrapping_for_order' => $testData['wrapping']));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_order',
            array('gift_wrapping_for_order' => $testData['wrapping']));
        $checkoutData =
            $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login', array('email' => $testData['email']),
                array('product_1'             => $simple1['simple']['product_name'],
                      'product_2'             => $simple2['simple']['product_name'],
                      'gift_options_address1' => $forProduct1,
                      'gift_options_address2' => $forProduct2));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($backendSettings);
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->fillProductTab($productSettings, 'gift_options');
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->frontend();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Preconditions:</p>
     * <ol>
     * <li>Simple products are created;</li>
     * <li>Customer is created;</li>
     * <li>Gift wrapping and messages On Item Level are set to "Yes" in system configuration.</li>
     * <li>Gift wrapping and messages On Order Level are set to "No" in system configuration.</li>
     * <li>Gift wrapping and messages are set to "No" in product settings for product#1 only.</li>
     * </ol>
     * <p>Steps:</p>
     * <ol>
     * <li>Log into Frontend;</li>
     * <li>Add 2 different products to the shopping cart;</li>
     * <li>Click "Checkout with Multiple Addresses" link;</li>
     * <li>Select different shipping addresses for items;</li>
     * <li>Click button "Continue to shipping information";</li>
     * <li>Select "Flat Rate" shipping methods for items;</li>
     * </ol>
     * <p>Expected Results:</p>
     * <ol><li>
     * For product#1 with settings on product level:
     * "Add Gift Options for Individual Items" checkbox isn't present;
     * "Add Gift Options for the Entire Order" checkbox isn't present;
     * </li>
     * <li>
     * For product#2 without settings on product level:
     * "Add Gift Options for the Entire Order" checkbox isn't present;
     * "Add Gift Options for Individual Items" checkbox is present;
     * "Gift Wrapping" for Individual Items is present;
     * "Gift Message" for Individual Items is present;
     * </li></ol>
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
        $backendSettings = $this->loadDataSet('GiftMessage', 'ind_items_all_yes_order_all_no');
        $productSettings = $this->loadDataSet('GiftWrapping', 'gift_options_message_no_wrapping_no');
        $search = $this->loadDataSet('Product', 'product_search', $simple1['simple']);
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_item',
            array('product_name'            => $simple2['simple']['product_name'],
                  'gift_wrapping_for_item'  => $testData['wrapping']));
        $checkout =
            $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login', array('email' => $testData['email']),
                array('product_1'             => $simple1['simple']['product_name'],
                      'product_2'             => $simple2['simple']['product_name'],
                      'gift_options_address2' => $forProduct2));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($backendSettings);
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->fillProductTab($productSettings, 'gift_options');
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->frontend();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkout);
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Preconditions:</p>
     * <ol>
     * <li>Simple products are created;</li>
     * <li>Customer is created;</li>
     * <li>Gift wrapping and messages On Item Level are set to "Yes" in system configuration.</li>
     * <li>Gift wrapping and messages On Order Level are set to "Yes"/"No"(multiple cases) in system configuration</li>
     * <li>Gift wrapping and messages are set to "No" in product settings for product#1 only.</li>
     * </ol>
     * <p>Steps:</p>
     * <ol>
     * <li>Log into Frontend;</li>
     * <li>Add 2 different products to the shopping cart;</li>
     * <li>Click "Checkout with Multiple Addresses" link;</li>
     * <li>Select different shipping addresses for items;</li>
     * <li>Click button "Continue to shipping information";</li>
     * <li>Select "Flat Rate" shipping methods for items;</li>
     * </ol>
     * <p>Expected Results:</p>
     * <ol><li>
     * For product#1 with settings on product level:
     * "Add Gift Options for Individual Items" checkbox is present;
     * "Gift Wrapping" for Individual Items is present;
     * "Gift Message" for Individual Items is present;
     * "Add Gift Options for the Entire Order" checkbox is present;
     * "Gift Wrapping" for Entire Order is/isn't present;
     * "Gift Message" for Entire Order isn't/is present;
     * </li>
     * <li>
     * For product#2 without settings on product level:
     * "Add Gift Options for the Entire Order" checkbox is present;
     * "Gift Wrapping" for Entire Order is/isn't present:
     * "Gift Message" for Entire Order isn't/is present;
     * "Add Gift Options for Individual Items" checkbox isn't present;
     * </li></ol>
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
        //Data
        list($simple1, $simple2) = $testData['products'];
        $backendSettings = $this->loadDataSet('GiftMessage', $backendData);
        $productSettings = $this->loadDataSet('GiftWrapping', 'gift_options_message_no_wrapping_no');
        $search = $this->loadDataSet('Product', 'product_search', $simple1['simple']);

        if ($backendData == 'ind_items_all_yes_order_wrapping_yes_message_no') {
            $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order',
                array('gift_wrapping_for_order' => $testData['wrapping']));
            $forProduct2 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order_mess_yes_wrap_yes_item',
                    array('product_name'            => $simple2['simple']['product_name'],
                          'gift_wrapping_for_item'  => $testData['wrapping'],
                          'gift_wrapping_for_order' => $testData['wrapping']));
        } else {
            $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order');
            $forProduct2 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order_mess_yes_wrap_yes_item',
                    array('product_name'            => $simple2['simple']['product_name'],
                          'gift_wrapping_for_item'  => $testData['wrapping']));
        }
        $checkout =
            $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login', array('email' => $testData['email']),
                array('product_1'             => $simple1['simple']['product_name'],
                      'product_2'             => $simple2['simple']['product_name'],
                      'gift_options_address1' => $forProduct1,
                      'gift_options_address2' => $forProduct2));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($backendSettings);
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->fillProductTab($productSettings, 'gift_options');
        $this->saveForm('save');
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
     * <p>Preconditions:</p>
     * <ol>
     * <li>Simple products are created;</li>
     * <li>Customer is created;</li>
     * <li>Gift wrapping and messages On Item Level are set to "Yes" in system configuration.</li>
     * <li>Gift wrapping and messages On Order Level are set to "Yes"/"No"(multiple cases) in system configuration</li>
     * <li><b>Gift wrapping is set to "No" in product settings for product#1 only.</b></li>
     * <li><b>Gift messages is set to "Yes" in product settings for product#1 only.</b></li>
     * </ol>
     * <p>Steps:</p>
     * <ol>
     * <li>Log into Frontend;</li>
     * <li>Add 2 different products to the shopping cart;</li>
     * <li>Click "Checkout with Multiple Addresses" link;</li>
     * <li>Select different shipping addresses for items;</li>
     * <li>Click button "Continue to shipping information";</li>
     * <li>Select "Flat Rate" shipping methods for items;</li>
     * </ol>
     * <p>Expected Results:</p>
     * <ol>
     * <li>For product#1 with settings on product level:</li>
     * <li>"Add Gift Options for Individual Items", "Gift Message" for individual items is present;</li>
     * <li>"Gift wrapping" for individual items is <b>not</b> present;</li>
     * <li>For product#2 w/o settings on product level
     * "Add Gift Options for Individual Items", "Gift Message", "Gift wrapping" for individual items are present;</li>
     * </ol>
     *
     * @param string $backendData
     * @param array $testData
     *
     * @test
     * @dataProvider giftWrappingAndMessageForItemAvailableButNotForProductDataProvider
     * @depends preconditionsForTests
     */
    public function giftWrappingAndMessageForItemAvailableButGiftWrappingForProductIsNot($backendData, $testData)
    {
        //Data
        list($simple1, $simple2) = $testData['products'];
        $backendSettings = $this->loadDataSet('GiftMessage', $backendData);
        $productSettings = $this->loadDataSet('GiftWrapping', 'gift_options_message_yes_wrapping_no');
        $search = $this->loadDataSet('Product', 'product_search', $simple1['simple']);
        if ($backendData == 'ind_items_all_yes_order_wrapping_yes_message_no') {
            $forProduct1 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order_mess_yes_wrap_no_item',
                    array('product_name'            => $simple1['simple']['product_name'],
                          'gift_wrapping_for_order' => $testData['wrapping']));
            $forProduct2 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order_mess_yes_wrap_yes_item',
                    array('product_name'            => $simple2['simple']['product_name'],
                          'gift_wrapping_for_item'  => $testData['wrapping'],
                          'gift_wrapping_for_order' => $testData['wrapping']));
        } else {
            $forProduct1 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order_mess_yes_wrap_no_item',
                    array('product_name' => $simple1['simple']['product_name']));
            $forProduct2 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order_mess_yes_wrap_yes_item',
                    array('product_name'           => $simple2['simple']['product_name'],
                          'gift_wrapping_for_item' => $testData['wrapping']));
        }
        $checkout =
            $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login', array('email' => $testData['email']),
                array('product_1'             => $simple1['simple']['product_name'],
                      'product_2'             => $simple2['simple']['product_name'],
                      'gift_options_address1' => $forProduct1,
                      'gift_options_address2' => $forProduct2));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($backendSettings);
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->fillProductTab($productSettings, 'gift_options');
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->frontend();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkout);
        $this->assertMessagePresent('success', 'success_checkout');
    }


    /**
     * <p>Preconditions:</p>
     * <ol>
     * <li>Simple products are created;</li>
     * <li>Customer is created;</li>
     * <li>Gift wrapping and messages On Item Level are set to "Yes" in system configuration.</li>
     * <li>Gift wrapping and messages On Order Level are set to "Yes"/"No"(multiple cases) in system configuration</li>
     * <li><b>Gift wrapping is set to "Yes" in product settings for product#1 only.</b></li>
     * <li><b>Gift messages is set to "No" in product settings for product#1 only.</b</li>
     * </ol>
     * <p>Steps:</p>
     * <ol>
     * <li>Log into Frontend;</li>
     * <li>Add 2 different products to the shopping cart;</li>
     * <li>Click "Checkout with Multiple Addresses" link;</li>
     * <li>Select different shipping addresses for items;</li>
     * <li>Click button "Continue to shipping information";</li>
     * <li>Select "Flat Rate" shipping methods for items;</li>
     * </ol>
     * <p>Expected Results:</p>
     * <ol>
     * <li>For product#1 with settings on product level:</li>
     * <li>"Add Gift Options for Individual Items", "Gift Wrapping" for individual items is present;</li>
     * <li>"Gift Message" for individual items is <b>not</b> present;</li>
     * <li>For product#2 w/o settings on product level
     * "Add Gift Options for Individual Items", "Gift Message", "Gift wrapping" for individual items are present;</li>
     * </ol>
     *
     * @param string $backendData
     * @param array $testData
     *
     * @test
     * @dataProvider giftWrappingAndMessageForItemAvailableButNotForProductDataProvider
     * @depends preconditionsForTests
     */
    public function giftWrappingAndMessageForItemAvailableButGiftMessageForProductIsNot($backendData, $testData)
    {
        //Data
        list($simple1, $simple2) = $testData['products'];
        $backendSettings = $this->loadDataSet('GiftMessage', $backendData);
        $productSettings = $this->loadDataSet('GiftWrapping', 'gift_options_message_no_wrapping_yes');
        $search = $this->loadDataSet('Product', 'product_search', $simple1['simple']);

        if ($backendData == 'ind_items_all_yes_order_wrapping_yes_message_no') {
            $forProduct1 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order_mess_no_wrap_yes_item',
                    array('product_name'            => $simple1['simple']['product_name'],
                          'gift_wrapping_for_item'  => $testData['wrapping'],
                          'gift_wrapping_for_order' => $testData['wrapping']));
            $forProduct2 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order_mess_yes_wrap_yes_item',
                    array('product_name'            => $simple2['simple']['product_name'],
                          'gift_wrapping_for_item'  => $testData['wrapping'],
                          'gift_wrapping_for_order' => $testData['wrapping']));
        } else {
            $forProduct1 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order_mess_no_wrap_yes_item',
                    array('product_name'           => $simple1['simple']['product_name'],
                          'gift_wrapping_for_item' => $testData['wrapping']));
            $forProduct2 =
                $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order_mess_yes_wrap_yes_item',
                    array('product_name'           => $simple2['simple']['product_name'],
                          'gift_wrapping_for_item' => $testData['wrapping']));
        }
        $checkout =
            $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login', array('email' => $testData['email']),
                array('product_1'             => $simple1['simple']['product_name'],
                      'product_2'             => $simple2['simple']['product_name'],
                      'gift_options_address1' => $forProduct1,
                      'gift_options_address2' => $forProduct2));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($backendSettings);
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->fillProductTab($productSettings, 'gift_options');
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->frontend();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkout);
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Preconditions:</p>
     * <ol>
     * <li>Simple product is created;</li>
     * <li>Customer is created;</li>
     * <li>Gift wrapping is created;</li>
     * <li>Gift wrapping for items and order are set to "Yes" in system configuration.</li>
     * <li>Price for gift wrapping is set in system configuration.</li>
     * <li>Different price for gift wrapping is set for one product.</li>
     * <li>In System-Configuration-Sales-Tax "Tax Class for Gift options" set to "None"</li>
     * </ol>
     * <p>Steps:</p>
     * <ol>
     * <li>Log into Frontend;</li>
     * <li>Add 2 products to the shopping cart;</li>
     * <li>Click "Checkout with Multiple Addresses" link;</li>
     * <li>Select different shipping addresses for items;</li>
     * <li>Click button "Continue to shipping information";</li>
     * <li>Select "Flat Rate" shipping method for item;</li>
     * <li>Check "Add gift options" checkbox;</li>
     * <li>Check "Add Gift Options for Entire Order" checkbox;</li>
     * <li>Select Gift Wrapping from "Gift Wrapping Design" dropdown;</li>
     * <li>Check "Add gift options for Individual Items" checkbox in the second item.</li>
     * <li>Select Gift Wrapping from "Gift Wrapping Design" dropdown for item;</li>
     * <li>Proceed to billing information page;</li>
     * <li>Select "Check/Money Order" payment method;</li>
     * <li>Proceed to review order information;</li>
     * <li>Check presence of gift wrapping for item and entire order in totals;</li>
     * <li>Submit order;</li>
     * </ol>
     * <p>Expected Results:</p>
     * <ol>
     * <li>Gift wrapping is mentioned in totals and its price is correct;</li>
     * <li>Order is created;</li>
     * </ol>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftWrappingForItemAndOrderCustomPriceForProduct($testData)
    {
        //Data
        list($simple1, $simple2) = $testData['products'];
        $backendSettings = $this->loadDataSet('GiftMessage', 'gift_message_and_wrapping_all_enable');
        $productSettings = $this->loadDataSet('GiftWrapping', 'gift_options_use_default',
            array('gift_options_price_for_gift_wrapping' => '1.23'));
        $search = $this->loadDataSet('Product', 'product_search', $simple1['simple']);
        $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_order_mess_yes_wrap_yes_item',
            array('product_name'            => $simple1['simple']['product_name'],
                  'gift_wrapping_for_item'  => $testData['wrapping'],
                  'gift_wrapping_for_order' => $testData['wrapping']));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_order_mess_yes_wrap_yes_item',
            array('product_name'            => $simple2['simple']['product_name'],
                  'gift_wrapping_for_item'  => $testData['wrapping'],
                  'gift_wrapping_for_order' => $testData['wrapping']));
        $checkoutData =
            $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login', array('email' => $testData['email']),
                array('product_1'             => $simple1['simple']['product_name'],
                      'product_2'             => $simple2['simple']['product_name'],
                      'gift_options_address1' => $forProduct1,
                      'gift_options_address2' => $forProduct2));
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($backendSettings);
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->fillProductTab($productSettings, 'gift_options');
        $this->saveForm('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->frontend();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Preconditions:</p>
     * <ol>
     * <li>Simple product is created;</li>
     * <li>Customer is created;</li>
     * <li>Web site is created;</li>
     * <li>Gift wrapping is created;</li>
     * <li>Gift wrapping for items and order are set to "Yes" in system configuration.</li>
     * <li>Set Catalog Price Scope "Website" in system configuration.</li>
     * <li>Price for gift wrapping is set to N1 in Manage Gift Wrapping.</li>
     * <li>Price for gift wrapping is set to N2 for the product on Default Values level.</li>
     * <li>Price for gift wrapping is set to N3 for the product on the created website level.</li>
     * <li>In System-Configuration-Sales-Tax "Tax Class for Gift options" set to "None"</li>
     * </ol>
     * <p>Steps:</p>
     * <ol>
     * <li>Log to <b>the specific website</b> in frontend;</li>
     * <li>Add 2 products to the shopping cart;</li>
     * <li>Click "Checkout with Multiple Addresses" link;</li>
     * <li>Select different shipping addresses for items;</li>
     * <li>Click button "Continue to shipping information";</li>
     * <li>Select "Flat Rate" shipping method for item;</li>
     * <li>Check "Add gift options" checkbox;</li>
     * <li>Check "Add Gift Options for Entire Order" checkbox;</li>
     * <li>Select Gift Wrapping from "Gift Wrapping Design" dropdown;</li>
     * <li>Check "Add gift options for Individual Items" checkbox in the second item.</li>
     * <li>Select Gift Wrapping from "Gift Wrapping Design" dropdown for item;</li>
     * <li>Proceed to billing information page;</li>
     * <li>Select "Check/Money Order" payment method;</li>
     * <li>Proceed to review order information;</li>
     * <li>Check presence of gift wrapping for item and entire order in totals;</li>
     * <li>Submit order;</li>
     * </ol>
     * <p>Expected Results:</p>
     * <ol>
     * <li>Gift wrapping is mentioned in totals and its price is N3 (as on the website level);</li>
     * <li>Order is created;</li>
     * </ol>
     *
     * @depends preconditionsForTestsWithWebSite
     * @param array $testData
     *
     * @test
     * @TestlinkId TL-MAGE-1044
     */
    public function giftWrappingPriceOnProductLevelForStoreView($testData)
    {
        //Data
        $productGiftSettings = $this->loadDataSet('GiftWrapping', 'gift_options_custom_wrapping_price');
        $productGiftSettingsOnStoreView = $this->loadDataSet('GiftWrapping',
                                                 'gift_options_custom_wrapping_price_on_store_view');
        $this->assertNotEquals($productGiftSettings['gift_options_price_for_gift_wrapping'],
                               $productGiftSettingsOnStoreView['gift_options_price_for_gift_wrapping']);
        $this->assertNotEquals($productGiftSettings['gift_options_price_for_gift_wrapping'],
                               $testData['wrapping']['gift_wrapping_price']);
        $website = $testData['website'];
        $product1 = $testData['products'][0]; // This product will have custom gift option settings
        $product2 = $testData['products'][1];
        $giftWrappingName = $testData['wrapping']['gift_wrapping_design'];
        $forProduct1 =
            $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order_mess_no_wrap_yes_item',
                array('product_name'            => $product1,
                      'gift_wrapping_for_item'  => $giftWrappingName,
                      'gift_wrapping_for_order' => $giftWrappingName));
        $checkoutData =
            $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
                array('email' => $testData['email'],
                      'product_qty' => 1),
                array('product_1'             => $product1,
                      'product_2'             => $product2,
                      'gift_options_address1' => $forProduct1,
                      'gift_options_address2' => '%noValue%'));
        $verifyPrices = $this->loadDataSet('MultipleAddressesCheckout', 'verify_prices_gift_wrapping_TL-MAGE-1044', null,
            array('product1'         => $product1,
                  'product2'         => $product2,
                  'product1wrapping' => $giftWrappingName));
        $checkoutData['verify_prices'] = $verifyPrices;
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_wrapping_enable_order_items');
        $this->systemConfigurationHelper()->configure('gift_options_website_price_scope');
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_name' => $product1));
        $this->chooseOkOnNextConfirmation();
        $this->fillForm(array('choose_store_view' => 'Default Values'));
        $this->productHelper()->fillTab($productGiftSettings, 'gift_options');
        $this->addParameter('tabId', '0');
        $this->addParameter('storeId', '0');
        $this->clickButton('save_and_continue_edit');
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->chooseOkOnNextConfirmation();
        $this->storeHelper()->selectStoreView('choose_store_view', $website['staging_website_name']);
        $this->productHelper()->fillTab($productGiftSettingsOnStoreView, 'gift_options');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $newFrontendUrl = $this->stagingWebsiteHelper()->buildFrontendUrl($website['staging_website_code']);
        $this->_configHelper->setAreaBaseUrl('frontend', $newFrontendUrl);
        $orderId = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderId) == 2, 'Expected that exactly 2 orders have been created.');
    }
}
