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
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage', 'gift_options_disable_all'));
    }

    protected function tearDownAfterTest()
    {
        $this->frontend();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
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
}
