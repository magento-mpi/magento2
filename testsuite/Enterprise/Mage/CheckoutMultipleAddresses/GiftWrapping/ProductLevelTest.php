<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Tests for Checkout with Multiple Addresses with gift wrapping and messages.
 * Verifies settings on product level. Frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_CheckoutMultipleAddresses_GiftWrapping_ProductLevelTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_options_disable_all');
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Create a new customer for tests</p>
     *
     * @test
     * @return array Customer 'email' and 'password'
     */
    public function preconditionsCreateCustomer()
    {
        $userData = $this->loadData('customer_account_for_prices_validation', null, 'email');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * <p>Creates 2 simple products for multiple checkout</p>
     *
     * @test
     * @return array Product Data
     */
    public function preconditionsCreateProducts()
    {
        //Data
        $productData1 = $this->loadData('simple_product_for_order');
        $productData2 = $this->loadData('simple_product_for_order');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData1);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($productData2);
        $this->assertMessagePresent('success', 'success_saved_product');
        return array($productData1, $productData2);
    }

    /**
     * <p>Creating a gift wrapping for tests</p>
     *
     * @test
     * @return array Gift wrapping data
     */
    public function preconditionsCreateGiftWrapping()
    {
        //Data
        $giftWrappingData = $this->loadData('gift_wrapping_without_image');
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        return $giftWrappingData;
    }

    /**
     * <p>Preconditions:</p>
     * <ol>
     * <li>Simple product is created;</li>
     * <li>Customer is created;</li>
     * <li>Gift wrapping is created;</li>
     * <li>Gift wrapping and messages are set to "No" in system configuration.</li>
     * <li>Gift wrapping and messages are set to "Yes" in product settings.</li>
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
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProducts
     * @depends preconditionsCreateGiftWrapping
     * @param array $customer
     * @param array $products
     * @param array $giftWrapping
     *
     * @test
     */
    public function giftWrappingAndMessageAvailableForItemAndOrder($customer, $products, $giftWrapping)
    {
        //Data
        $backendSettings = 'gift_message_and_wrapping_all_enable';
        $productGiftSettings = 'gift_options_message_yes_wrapping_yes';
        $productName = $products[0]['general_name'];
        $giftWrappingName = $giftWrapping['gift_wrapping_design'];
        $individualItemsMessage = $this->loadData('gift_message_for_individual_items');
        $indItems = array($productName =>
                          array('item_gift_wrapping_design' => $giftWrappingName,
                                'gift_message'              => $individualItemsMessage));
        $checkoutData = $this->loadData('multiple_with_gift_wrapping_and_message',
                    array('email' => $customer['email'],
                          'password' => $customer['password'],
                          'order_gift_wrapping_design' => $giftWrappingName,
                          'individual_items' => $indItems,
                          'product_name' => $productName . ' Gift Wrapping Design : ' . $giftWrappingName));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productName;
        $checkoutData['products_to_add']['product_1']['general_name'] = $productName;
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($backendSettings);
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_name' => $productName));
        $this->productHelper()->fillTab($productGiftSettings, 'gift_options');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Test cases: TL-MAGE-855</p>
     * <p>Preconditions:</p>
     * <ol>
     * <li>Simple products are created;</li>
     * <li>Customer is created;</li>
     * <li>Gift wrapping and messages On Item Level are set to "No" in system configuration.</li>
     * <li>Gift wrapping and messages On Order Level are set to "Yes" in system configuration.</li>
     * <li>Gift wrapping and messages are set to "Yes" in product settings for product#2 only.</li>
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
     * <li>For product#2 with settings on product level
     * "Add Gift Options for Individual Items", "Gift Message", "Gift wrapping" for individual items are present;</li>
     * <li>For product#1 w/o settings on product level
     * "Add Gift Options" checkbox is absent;</li>
     * </ol>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProducts
     * @param array $customer
     * @param array $products
     *
     * @test
     */
    public function giftWrappingAndMessageForItemAvailableForProductOnly($customer, $products)
    {
        //Data
        $backendSettings = 'ind_items_gift_wrapping_no_message_no';
        $productGiftSettings = 'gift_options_message_yes_wrapping_yes';
        $productName1 = $products[0]['general_name']; // This product will have custom gift option settings
        $productName2 = $products[1]['general_name'];
        $checkoutData = $this->loadData('multiple_for_two_addresses_for_order_only',
                                array('email'       => $customer['email'],
                                      'password'    => $customer['password']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productName1;
        $checkoutData['shipping_address_data']['address_to_ship_2']['general_name'] = $productName2;
        $checkoutData['products_to_add']['product_1']['general_name'] = $productName1;
        $checkoutData['products_to_add']['product_2']['general_name'] = $productName2;
        $shippingInfoData = $this->loadData('shipping_data_for_two_addresses');
        $shippingInfoData = $this->arrayEmptyClear($shippingInfoData);
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($backendSettings);
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_name' => $productName1));
        $this->productHelper()->fillTab($productGiftSettings, 'gift_options');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingInfoData, false);
        //Verification for the 1st product with custom settings
        $shippingAddress1 = $shippingInfoData['address_1']['search_shipping_address'];
        $this->checkoutMultipleAddressesHelper()->setAddressHeader($shippingAddress1);
        $this->addParameter('productName', $productName1);
        $this->fillForm(array('add_gift_options'                 => 'Yes',
                              'gift_option_for_item'             => 'Yes'));
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertTrue($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'item_gift_wrapping_design')),
                        'Gift wrapping for individual items is not available for product ' . $productName1);
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('link', 'gift_message_for_item')),
                        'Gift message for individual items is not available for product ' . $productName1);
        //Verification for the 2nd product with gift options
        $shippingAddress2 = $shippingInfoData['address_2']['search_shipping_address'];
        $this->checkoutMultipleAddressesHelper()->setAddressHeader($shippingAddress2);
        $this->addParameter('productName', $productName2);
        $this->fillForm(array('add_gift_options' => 'Yes'));
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertFalse($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('checkbox', 'gift_option_for_item')),
                        'Gift options checkbox for individual items is available for product ' . $productName2);
    }

    /**
     * <p>Test cases: TL-MAGE-862</p>
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
     * <ol>
     * <li>For product#1 with settings on product level
     * "Add Gift Options" checkbox is absent;</li>
     * <li>For product#2 w/o settings on product level
     * "Add Gift Options for Individual Items", "Gift Message", "Gift wrapping" for individual items are present;</li>
     * </ol>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProducts
     * @param array $customer
     * @param array $products
     *
     * @test
     */
    public function giftWrappingAndMessageForItemOnlyAvailableButNotForProduct($customer, $products)
    {
        //Data
        $backendSettings = 'ind_items_all_yes_order_all_no';
        $productGiftSettings = 'gift_options_message_no_wrapping_no';
        $productName1 = $products[0]['general_name']; // This product will have custom gift option settings
        $productName2 = $products[1]['general_name'];
        $checkoutData = $this->loadData('multiple_for_two_addresses_for_order_only',
                                array('email'       => $customer['email'],
                                      'password'    => $customer['password']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productName1;
        $checkoutData['shipping_address_data']['address_to_ship_2']['general_name'] = $productName2;
        $checkoutData['products_to_add']['product_1']['general_name'] = $productName1;
        $checkoutData['products_to_add']['product_2']['general_name'] = $productName2;
        $shippingInfoData = $this->loadData('shipping_data_for_two_addresses');
        $shippingInfoData = $this->arrayEmptyClear($shippingInfoData);
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($backendSettings);
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_name' => $productName1));
        $this->productHelper()->fillTab($productGiftSettings, 'gift_options');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingInfoData, false);
        //Verification for the 1st product with custom settings
        $shippingAddress1 = $shippingInfoData['address_1']['search_shipping_address'];
        $this->checkoutMultipleAddressesHelper()->setAddressHeader($shippingAddress1);
        $this->addParameter('productName', $productName1);
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertFalse($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('checkbox', 'add_gift_options')),
                        'Add Gift Options checkbox is available for product ' . $productName1);
        //Verification for the 2nd product with gift options
        $shippingAddress2 = $shippingInfoData['address_2']['search_shipping_address'];
        $this->checkoutMultipleAddressesHelper()->setAddressHeader($shippingAddress2);
        $this->addParameter('productName', $productName2);
        $this->fillForm(array('add_gift_options'                 => 'Yes',
                              'gift_option_for_item'             => 'Yes'));
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertTrue($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'item_gift_wrapping_design')),
                        'Gift wrapping for individual items is not available for product ' . $productName2);
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('link', 'gift_message_for_item')),
                        'Gift message for individual items is not available for product ' . $productName2);

    }

    /**
     * <p>Test cases: TL-MAGE-863</p>
     * <p>Preconditions:</p>
     * <ol>
     * <li>Simple products are created;</li>
     * <li>Customer is created;</li>
     * <li>Gift wrapping and messages On Item Level are set to "Yes" in system configuration.</li>
     * <li>Gift wrapping and messages On Order Level are set to "Yes"/"No" (multiple cases) in system configuration.</li>
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
     * <ol>
     * <li>For product#1 with settings on product level
     * "Add Gift Options for Individual Items", are absent;</li>
     * <li>For product#2 w/o settings on product level
     * "Add Gift Options for Individual Items", "Gift Message", "Gift wrapping" for individual items are present;</li>
     * </ol>
     *
     * @dataProvider giftWrappingAndMessageForItemAvailableButNotForProductDataProvider
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProducts
     * @param array $backendSettings
     * @param array $customer
     * @param array $products
     *
     * @test
     */
    public function giftWrappingAndMessageForItemAvailableButNotForProduct($backendSettings, $customer, $products)
    {
        //Data
        $productGiftSettings = 'gift_options_message_no_wrapping_no';
        $productName1 = $products[0]['general_name']; // This product will have custom gift option settings
        $productName2 = $products[1]['general_name'];
        $checkoutData = $this->loadData('multiple_for_two_addresses_for_order_only',
                                array('email'       => $customer['email'],
                                      'password'    => $customer['password']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productName1;
        $checkoutData['shipping_address_data']['address_to_ship_2']['general_name'] = $productName2;
        $checkoutData['products_to_add']['product_1']['general_name'] = $productName1;
        $checkoutData['products_to_add']['product_2']['general_name'] = $productName2;
        $shippingInfoData = $this->loadData('shipping_data_for_two_addresses');
        $shippingInfoData = $this->arrayEmptyClear($shippingInfoData);
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($backendSettings);
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_name' => $productName1));
        $this->productHelper()->fillTab($productGiftSettings, 'gift_options');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingInfoData, false);
        //Verification for the 1st product with custom settings
        $shippingAddress1 = $shippingInfoData['address_1']['search_shipping_address'];
        $this->checkoutMultipleAddressesHelper()->setAddressHeader($shippingAddress1);
        $this->addParameter('productName', $productName1);
        $this->fillForm(array('add_gift_options'                 => 'Yes'));
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertFalse($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'item_gift_wrapping_design')),
                        'Gift wrapping for individual items is available for product ' . $productName1);
        $this->assertFalse($this->isElementPresent(
                $this->_getControlXpath('link', 'gift_message_for_item')),
                        'Gift message for individual items is available for product ' . $productName1);
        //Verification for the 2nd product with gift options
        $shippingAddress2 = $shippingInfoData['address_2']['search_shipping_address'];
        $this->checkoutMultipleAddressesHelper()->setAddressHeader($shippingAddress2);
        $this->addParameter('productName', $productName2);
        $this->fillForm(array('add_gift_options'                 => 'Yes',
                              'gift_option_for_item'             => 'Yes'));
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertTrue($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'item_gift_wrapping_design')),
                        'Gift wrapping for individual items is not available for product ' . $productName2);
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('link', 'gift_message_for_item')),
                        'Gift message for individual items is not available for product ' . $productName2);

    }

    public function giftWrappingAndMessageForItemAvailableButNotForProductDataProvider()
    {
        return array(
          array('ind_items_all_yes_order_wrapping_yes_message_no'),
          array('ind_items_all_yes_order_wrapping_no_message_yes')
        );
    }

    /**
     * <p>Test cases: TL-MAGE-864</p>
     * <p>Preconditions:</p>
     * <ol>
     * <li>Simple products are created;</li>
     * <li>Customer is created;</li>
     * <li>Gift wrapping and messages On Item Level are set to "Yes" in system configuration.</li>
     * <li>Gift wrapping and messages On Order Level are set to "Yes"/"No" (multiple cases) in system configuration.</li>
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
     * @dataProvider giftWrappingAndMessageForItemAvailableButGiftWrappingForProductIsNotDataProvider
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProducts
     * @param array $backendSettings
     * @param array $customer
     * @param array $products
     *
     * @test
     */
    public function giftWrappingAndMessageForItemAvailableButGiftWrappingForProductIsNot
                                               ($backendSettings, $customer, $products)
    {
        //Data
        $productGiftSettings = 'gift_options_message_yes_wrapping_no';
        $productName1 = $products[0]['general_name']; // This product will have custom gift option settings
        $productName2 = $products[1]['general_name'];
        $checkoutData = $this->loadData('multiple_for_two_addresses_for_order_only',
                                array('email'       => $customer['email'],
                                      'password'    => $customer['password']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productName1;
        $checkoutData['shipping_address_data']['address_to_ship_2']['general_name'] = $productName2;
        $checkoutData['products_to_add']['product_1']['general_name'] = $productName1;
        $checkoutData['products_to_add']['product_2']['general_name'] = $productName2;
        $shippingInfoData = $this->loadData('shipping_data_for_two_addresses');
        $shippingInfoData = $this->arrayEmptyClear($shippingInfoData);
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($backendSettings);
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_name' => $productName1));
        $this->productHelper()->fillTab($productGiftSettings, 'gift_options');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingInfoData, false);
        //Verification for the 1st product with custom settings
        $shippingAddress1 = $shippingInfoData['address_1']['search_shipping_address'];
        $this->checkoutMultipleAddressesHelper()->setAddressHeader($shippingAddress1);
        $this->addParameter('productName', $productName1);
        $this->fillForm(array('add_gift_options'                 => 'Yes'));
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertFalse($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'item_gift_wrapping_design')),
                        'Gift wrapping for individual items is available for product ' . $productName1);
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('link', 'gift_message_for_item')),
                        'Gift message for individual items is not available for product ' . $productName1);
        //Verification for the 2nd product with gift options
        $shippingAddress2 = $shippingInfoData['address_2']['search_shipping_address'];
        $this->checkoutMultipleAddressesHelper()->setAddressHeader($shippingAddress2);
        $this->addParameter('productName', $productName2);
        $this->fillForm(array('add_gift_options'                 => 'Yes',
                              'gift_option_for_item'             => 'Yes'));
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertTrue($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'item_gift_wrapping_design')),
                        'Gift wrapping for individual items is not available for product ' . $productName2);
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('link', 'gift_message_for_item')),
                        'Gift message for individual items is not available for product ' . $productName2);

    }

    public function giftWrappingAndMessageForItemAvailableButGiftWrappingForProductIsNotDataProvider()
    {
        return array(
          array('ind_items_all_yes_order_wrapping_yes_message_no'),
          array('ind_items_all_yes_order_wrapping_no_message_yes')
        );
    }

    /**
     * <p>Test cases: TL-MAGE-865</p>
     * <p>Preconditions:</p>
     * <ol>
     * <li>Simple products are created;</li>
     * <li>Customer is created;</li>
     * <li>Gift wrapping and messages On Item Level are set to "Yes" in system configuration.</li>
     * <li>Gift wrapping and messages On Order Level are set to "Yes"/"No" (multiple cases) in system configuration.</li>
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
     * @dataProvider giftWrappingAndMessageForItemAvailableButGiftMessageForProductIsNotDataProvider
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProducts
     * @param array $backendSettings
     * @param array $customer
     * @param array $products
     *
     * @test
     */
    public function giftWrappingAndMessageForItemAvailableButGiftMessageForProductIsNot
                                                    ($backendSettings, $customer, $products)
    {
        //Data
        $productGiftSettings = 'gift_options_message_no_wrapping_yes';
        $productName1 = $products[0]['general_name']; // This product will have custom gift option settings
        $productName2 = $products[1]['general_name'];
        $checkoutData = $this->loadData('multiple_for_two_addresses_for_order_only',
                                array('email'       => $customer['email'],
                                      'password'    => $customer['password']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productName1;
        $checkoutData['shipping_address_data']['address_to_ship_2']['general_name'] = $productName2;
        $checkoutData['products_to_add']['product_1']['general_name'] = $productName1;
        $checkoutData['products_to_add']['product_2']['general_name'] = $productName2;
        $shippingInfoData = $this->loadData('shipping_data_for_two_addresses');
        $shippingInfoData = $this->arrayEmptyClear($shippingInfoData);
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($backendSettings);
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_name' => $productName1));
        $this->productHelper()->fillTab($productGiftSettings, 'gift_options');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingInfoData, false);
        //Verification for the 1st product with custom settings
        $shippingAddress1 = $shippingInfoData['address_1']['search_shipping_address'];
        $this->checkoutMultipleAddressesHelper()->setAddressHeader($shippingAddress1);
        $this->addParameter('productName', $productName1);
        $this->fillForm(array('add_gift_options'                 => 'Yes'));
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertTrue($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'item_gift_wrapping_design')),
                        'Gift wrapping for individual items is not available for product ' . $productName1);
        $this->assertFalse($this->isElementPresent(
                $this->_getControlXpath('link', 'gift_message_for_item')),
                        'Gift message for individual items is available for product ' . $productName1);
        //Verification for the 2nd product with gift options
        $shippingAddress2 = $shippingInfoData['address_2']['search_shipping_address'];
        $this->checkoutMultipleAddressesHelper()->setAddressHeader($shippingAddress2);
        $this->addParameter('productName', $productName2);
        $this->fillForm(array('add_gift_options'                 => 'Yes',
                              'gift_option_for_item'             => 'Yes'));
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertTrue($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'item_gift_wrapping_design')),
                        'Gift wrapping for individual items is not available for product ' . $productName2);
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('link', 'gift_message_for_item')),
                        'Gift message for individual items is not available for product ' . $productName2);

    }

    public function giftWrappingAndMessageForItemAvailableButGiftMessageForProductIsNotDataProvider()
    {
        return array(
          array('ind_items_all_yes_order_wrapping_yes_message_no'),
          array('ind_items_all_yes_order_wrapping_no_message_yes')
        );
    }

    /**
     * <p>Test cases: TL-MAGE-887</p>
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
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProducts
     * @depends preconditionsCreateGiftWrapping
     * @param array $customer
     * @param array $products
     * @param array $giftWrapping
     *
     * @test
     */
    public function giftWrappingForItemAndOrderCustomPriceForProduct($customer, $products, $giftWrapping)
    {
        //Data
        $productGiftSettings = 'gift_options_custom_wrapping_price';
        $productName1 = $products[0]['general_name']; // This product will have custom gift option settings
        $productName2 = $products[1]['general_name'];
        $giftWrappingName = $giftWrapping['gift_wrapping_design'];
        $indItems1 = array($productName1 => array('item_gift_wrapping_design'  => $giftWrappingName));
        $indItems2 = array($productName2 => array('item_gift_wrapping_design'  => $giftWrappingName));
        $checkoutData = $this->loadData('multiple_for_two_addresses_verify_wrapping_prices',
                    array('email'       => $customer['email'],
                          'password'    => $customer['password'],
                          'product_1/general_name'   => $productName1,
                          'product_2/general_name'   => $productName2,
                          'address_to_ship_1/general_name'   => $productName1,
                          'address_to_ship_2/general_name'   => $productName2,
                          'order_gift_wrapping_design' => $giftWrappingName,
                          ));
        $checkoutData['shipping_data']['address_1']['gift_options']['individual_items'] = $indItems1;
        $checkoutData['shipping_data']['address_2']['gift_options']['individual_items'] = $indItems2;
        $checkoutData['verify_products_data']['address_1']['validate_prod_data']['product_1']['product_name']
                                             = $productName1 . ' Gift Wrapping Design : ' . $giftWrappingName;
        $checkoutData['verify_products_data']['address_2']['validate_prod_data']['product_1']['product_name']
                                             = $productName2 . ' Gift Wrapping Design : ' . $giftWrappingName;

        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_wrapping_all_enable');
        $this->systemConfigurationHelper()->configure('tax_config_no_tax_for_gift_options');
        $this->navigate('manage_products');
        $this->productHelper()->openProduct(array('product_name' => $productName1));
        $this->productHelper()->fillTab($productGiftSettings, 'gift_options');
        $this->clickButton('save');
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->customerHelper()->frontLoginCustomer($customer);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $orderNums = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderNums) == 2, 'Expected that exactly 2 orders have been created.');
    }
}
