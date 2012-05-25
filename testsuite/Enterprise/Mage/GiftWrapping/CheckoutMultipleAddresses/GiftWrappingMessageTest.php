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
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage', 'gift_options_disable_all'));
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
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage', 'gift_options_disable_all'));
        $this->_configHelper->getConfigAreas(true);
    }

    /**
     * @return array
     * @test
     * @skipTearDown
     */
    public function preconditionsForTests()
    {
        //Data
        $website = $this->loadDataSet('StagingWebsite', 'staging_website');
        $websiteName = $website['general_information']['staging_website_name'];
        $websiteSettings = $this->loadDataSet('StagingWebsite', 'staging_website_enable_auto_entries');
        $productDefault = $this->loadDataSet('Product', 'simple_product_visible');
        $product = $this->loadDataSet('Product', 'simple_product_visible', array('websites' => $websiteName));
        $userDefault = $this->loadDataSet('Customers', 'generic_customer_account');
        $user =
            $this->loadDataSet('Customers', 'generic_customer_account', array('associate_to_website' => $websiteName));
        $wrappingDefault = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $wrapping = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image',
            array('gift_wrapping_websites' => $websiteName));
        //Steps and Verification
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($websiteSettings);

        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        $this->assertMessagePresent('success', 'success_created_website');

        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productDefault);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($product);
        $this->assertMessagePresent('success', 'success_saved_product');

        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userDefault);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->customerHelper()->createCustomer($user);
        $this->assertMessagePresent('success', 'success_saved_customer');

        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($wrappingDefault);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($wrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');

        return array('wrappingDefault' => $wrappingDefault['gift_wrapping_design'],
                     'simpleDefault'   => $productDefault['general_name'],
                     'userDefault'     => $userDefault['email'],
                     'simple'          => $product['general_name'],
                     'user'            => $user['email'],
                     'wrapping'        => $wrapping['gift_wrapping_design'],
                     'website'         => $websiteName,
                     'code'            => $website['general_information']['staging_website_code']);
    }

    /**
     * <p>Test cases: TL-MAGE-965 and TL-MAGE-961 and TL-MAGE-853 and TL-MAGE-871</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. Gift wrapping is created;</p>
     * <p>4. Gift wrapping and gift messages are allowed for entire order and individual items in system
     * configuration.</p>
     * <p>Steps:</p>
     * <p>1. Log into Frontend;</p>
     * <p>2. Add one product to shopping cart;</p>
     * <p>3. Click "Checkout with Multiple Addresses" link;</p>
     * <p>4. Select a different shipping address for item;</p>
     * <p>5. Click button "Continue to shipping information";</p>
     * <p>6. Select "Flat Rate" shipping method for item;</p>
     * <p>7. Check "Add gift options" checkbox;</p>
     * <p>8. Check "Add Gift Options for Entire Order" checkbox;</p>
     * <p>9. Select Gift Wrapping from "Gift Wrapping Design" dropdown;</p>
     * <p>10. Click "Gift Message" link for entire order;</p>
     * <p>11. Add gift message for entire order;</p>
     * <p>12. Check "Add gift options for Individual Items" checkbox in the second item.
     * <p>13. Select Gift Wrapping from "Gift Wrapping Design" dropdown for item;</p>
     * <p>14. Click "Gift Message" link for individual item;</p>
     * <p>15. Add gift message for individual item;<p>
     * <p>16. Proceed to billing information page;</p>
     * <p>17. Select "Check/Money Order" payment method;</p>
     * <p>18. Proceed to review order information;</p>
     * <p>19. Check presence of gift wrapping for item and entire order in totals;</p>
     * <p>20. Submit order;</p>
     * <p>Expected Results:</p>
     * <p>1. Gift wrapping is mentioned in totals and its price is correct;</p>
     * <p>2. Order is created;</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function withGiftWrappingAndMessage($testData)
    {
        //Data
        $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'all_variants_for_gift_options',
            array('product_name'            => $testData['simpleDefault'],
                  'gift_wrapping_for_item'  => $testData['wrappingDefault'],
                  'gift_wrapping_for_order' => $testData['wrappingDefault']));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'all_variants_for_gift_options',
            array('product_name'            => $testData['simpleDefault'],
                  'gift_wrapping_for_item'  => $testData['wrappingDefault'],
                  'gift_wrapping_for_order' => $testData['wrappingDefault']));
        $productsAdd = $this->loadDataSet('MultipleAddressesCheckout', 'products_to_add_with_qty', null,
            array('product_1' => $testData['simpleDefault'],
                  'product_2' => $testData['simpleDefault']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email'           => $testData['userDefault'],
                  'products_to_add' => $productsAdd), array('product_1'             => $testData['simpleDefault'],
                                                            'product_2'             => $testData['simpleDefault'],
                                                            'gift_options_address1' => $forProduct1,
                                                            'gift_options_address2' => $forProduct2));
        $verifyPrices = $this->loadDataSet('MultipleAddressesCheckout', 'verify_prices_all_gift_options', null,
            array('product1'         => $testData['simpleDefault'],
                  'product2'         => $testData['simpleDefault'],
                  'product1wrapping' => $testData['wrappingDefault'],
                  'product2wrapping' => $testData['wrappingDefault']));
        $checkoutData['verify_prices'] = $verifyPrices;
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage', 'gift_wrapping_all_enable'));
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage', 'gift_message_all_enable'));
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Test case:</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. Gift wrapping is created;</p>
     * <p>4. Gift wrapping and gift messages are allowed for entire order only.</p>
     * <p>Steps:</p>
     * <p>1. Log into Frontend;</p>
     * <p>2. Add one product to shopping cart;</p>
     * <p>3. Click "Checkout with Multiple Addresses" link;</p>
     * <p>4. Select a different shipping address for item;</p>
     * <p>5. Click button "Continue to shipping information";</p>
     * <p>6. Select "Flat Rate" shipping method for item;</p>
     * <p>7. Check "Add gift options" checkbox;</p>
     * <p>8. Verify "Gift Wrapping Design" dropdown for entire order is present;</p>
     * <p>9. Verify "Gift Message" for Entire Order is present;</p>
     * <p>10. Verify "Gift Wrapping Design" dropdown for individual items is not present;</p>
     * <p>11. Verify "Gift Message" for Individual Items is not present.</p>
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
            array('gift_wrapping_for_order' => $testData['wrappingDefault']));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_order',
            array('gift_wrapping_for_order' => $testData['wrappingDefault']));
        $productsAdd = $this->loadDataSet('MultipleAddressesCheckout', 'products_to_add_with_qty', null,
            array('product_1' => $testData['simpleDefault'],
                  'product_2' => $testData['simpleDefault']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email'           => $testData['userDefault'],
                  'products_to_add' => $productsAdd), array('product_1'             => $testData['simpleDefault'],
                                                            'product_2'             => $testData['simpleDefault'],
                                                            'gift_options_address1' => $forProduct1,
                                                            'gift_options_address2' => $forProduct2));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage',
            'order_gift_wrapping_yes_message_yes'));
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>TL-MAGE-970, TL-MAGE-962: Gift Wrapping for entire Order is not allowed (wrapping-no; message-no)</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. Gift wrapping is created;</p>
     * <p>4. Gift wrapping and gift messages are allowed for individual items only.</p>
     * <p>Steps:</p>
     * <p>1. Log into Frontend;</p>
     * <p>2. Add one product to shopping cart;</p>
     * <p>3. Click "Checkout with Multiple Addresses" link;</p>
     * <p>4. Select a different shipping address for item;</p>
     * <p>5. Click button "Continue to shipping information";</p>
     * <p>6. Select "Flat Rate" shipping method for item;</p>
     * <p>7. Check "Add gift options" checkbox;</p>
     * <p>8. Verify "Gift Wrapping Design" dropdown for entire order is not present;</p>
     * <p>9. Verify "Gift Message" for Entire Order is not present;</p>
     * <p>10. Verify "Gift Wrapping Design" dropdown for individual items is present;</p>
     * <p>11. Verify "Gift Message" for Individual Items is present.</p>
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
        $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_item',
            array('product_name'           => $testData['simpleDefault'],
                  'gift_wrapping_for_item' => $testData['wrappingDefault']));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_item',
            array('product_name'           => $testData['simpleDefault'],
                  'gift_wrapping_for_item' => $testData['wrappingDefault']));
        $productsAdd = $this->loadDataSet('MultipleAddressesCheckout', 'products_to_add_with_qty', null,
            array('product_1' => $testData['simpleDefault'],
                  'product_2' => $testData['simpleDefault']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email'           => $testData['userDefault'],
                  'products_to_add' => $productsAdd), array('product_1'             => $testData['simpleDefault'],
                                                            'product_2'             => $testData['simpleDefault'],
                                                            'gift_options_address1' => $forProduct1,
                                                            'gift_options_address2' => $forProduct2));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage',
            'ind_items_gift_wrapping_yes_message_yes'));
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>TL-MAGE-973:Gift Wrapping for Individual Items is not allowed (wrapping-no; message-yes)</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. Gift wrapping is created;</p>
     * <p>4. In system configuration setting "Allow Gift Messages for Individual Items" is set to "Yes";</p>
     * <p>5. In system configuration setting "Allow Gift Wrapping for Individual Items" is set to "No".</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend;</p>
     * <p>2. Add any product to a Cart and proceed to checkout with Multiple Addresses Checkout;</p>
     * <p>3. Select any different addresses in "select addresses" step and press button "Continue to shopping
     * information";</p>
     * <p>4. Select any shipping methods in shipping method tabs;</p>
     * <p>5. Check the "Add gift options" checkboxes;</p>
     * <p>6. Check "Add gift options for Individual Items" checkboxes;</p>
     *
     * <p>Expected Results:</p>
     * <p>1. Dropdown "Gift Wrapping Design" for individual items should not be shown.</p>
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
            array('product_name' => $testData['simpleDefault']));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_item',
            array('product_name' => $testData['simpleDefault']));
        $productsAdd = $this->loadDataSet('MultipleAddressesCheckout', 'products_to_add_with_qty', null,
            array('product_1' => $testData['simpleDefault'],
                  'product_2' => $testData['simpleDefault']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email'           => $testData['userDefault'],
                  'products_to_add' => $productsAdd), array('product_1'             => $testData['simpleDefault'],
                                                            'product_2'             => $testData['simpleDefault'],
                                                            'gift_options_address1' => $forProduct1,
                                                            'gift_options_address2' => $forProduct2));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage',
            'ind_items_gift_wrapping_no_message_yes'));
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>TL-MAGE-967:Gift Message for Individual Items is not allowed (message-no; wrapping-yes)</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. Gift wrapping is created;</p>
     * <p>4. In system configuration setting "Allow Gift Messages for Individual Items" is set to "No";</p>
     * <p>5. In system configuration setting "Allow Gift Wrapping for Individual Items" is set to "Yes".</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend;</p>
     * <p>2. Add any product to a Cart and proceed to checkout with Multiple Addresses Checkout;</p>
     * <p>3. Select any different addresses in "select addresses" step and press button "Continue to shopping
     * information";</p>
     * <p>4. Select any shipping methods in shipping method tabs;</p>
     * <p>5. Check the "Add gift options" checkbox;</p>
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
            array('product_name'           => $testData['simpleDefault'],
                  'gift_wrapping_for_item' => $testData['wrappingDefault']));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_item',
            array('product_name'           => $testData['simpleDefault'],
                  'gift_wrapping_for_item' => $testData['wrappingDefault']));
        $productsAdd = $this->loadDataSet('MultipleAddressesCheckout', 'products_to_add_with_qty', null,
            array('product_1' => $testData['simpleDefault'],
                  'product_2' => $testData['simpleDefault']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email'           => $testData['userDefault'],
                  'products_to_add' => $productsAdd), array('product_1'             => $testData['simpleDefault'],
                                                            'product_2'             => $testData['simpleDefault'],
                                                            'gift_options_address1' => $forProduct1,
                                                            'gift_options_address2' => $forProduct2));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage',
            'ind_items_gift_wrapping_yes_message_no'));
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>TL-MAGE-963:Gift Wrapping for entire Order is not allowed (wrapping-no; message-yes)</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. Gift wrapping is created;</p>
     * <p>4. In system configuration setting "Allow Gift Messages On Order Level" is set to "Yes";</p>
     * <p>5. In system configuration setting "Allow Gift Wrapping On Order Level" is set to "No".</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend;</p>
     * <p>2. Add any product to a Cart and proceed to checkout with Multiple Addresses Checkout;</p>
     * <p>3. Select any different addresses in "select addresses" step and press button "Continue to shopping
     * information";</p>
     * <p>4. Select any shipping methods in shipping method tabs;</p>
     * <p>5. Check "Add gift options" checkbox;</p>
     * <p>6. Verify dropdown "Gift Wrapping Design" is not visible for order;</p>
     * <p>7. Verify link "Gift Message" is visible for order;</p>
     *
     * <p>Expected Results:</p>
     * <p>1. "Gift Wrapping Design" dropdown is not visible for order;</p>
     * <p>2. "Gift Message" link is visible for order.</p>
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
        $productsAdd = $this->loadDataSet('MultipleAddressesCheckout', 'products_to_add_with_qty', null,
            array('product_1' => $testData['simpleDefault'],
                  'product_2' => $testData['simpleDefault']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email'           => $testData['userDefault'],
                  'products_to_add' => $productsAdd), array('product_1'             => $testData['simpleDefault'],
                                                            'product_2'             => $testData['simpleDefault'],
                                                            'gift_options_address1' => $forProduct1,
                                                            'gift_options_address2' => $forProduct2));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage',
            'order_gift_wrapping_no_message_yes'));
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Test Case TL-MAGE-960: Gift Message for entire Order is not allowed (message-no; wrapping-yes)</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. Gift wrapping is created;</p>
     * <p>4. In system configuration setting "Allow Gift Messages On Order Level" is set to "No";</p>
     * <p>5. In system configuration setting "Allow Gift Wrapping On Order Level" is set to "Yes".</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend;</p>
     * <p>2. Add any product to a Cart and proceed to checkout with Multiple Addresses Checkout;</p>
     * <p>3. Select any different addresses in "select addresses" step and press button "Continue to shopping
     * information";</p>
     * <p>4. Select any shipping methods in shipping method tabs;</p>
     * <p>5. Check the "Add gift options" checkbox;</p>
     * <p>6. Verify dropdown "Gift Wrapping Design" is visible for order;</p>
     * <p>7. Verify link "Gift Message" is not visible for order;</p>
     *
     * <p>Expected Results:</p>
     * <p>1. "Gift Wrapping Design" dropdown is visible for order;</p>
     * <p>2. "Gift Message" link is not visible for order.</p>
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
            array('gift_wrapping_for_order' => $testData['wrappingDefault']));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_no_wrap_yes_order',
            array('gift_wrapping_for_order' => $testData['wrappingDefault']));
        $productsAdd = $this->loadDataSet('MultipleAddressesCheckout', 'products_to_add_with_qty', null,
            array('product_1' => $testData['simpleDefault'],
                  'product_2' => $testData['simpleDefault']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email'           => $testData['userDefault'],
                  'products_to_add' => $productsAdd), array('product_1'             => $testData['simpleDefault'],
                                                            'product_2'             => $testData['simpleDefault'],
                                                            'gift_options_address1' => $forProduct1,
                                                            'gift_options_address2' => $forProduct2));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage',
            'order_gift_wrapping_yes_message_no'));
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Test Case TL-MAGE-992: Printed Card to Order is allowed</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. In system configuration setting "Allow Printed Card" is set to "Yes";</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend;</p>
     * <p>2. Add any product to a Cart and proceed to checkout with Multiple Addresses Checkout;</p>
     * <p>3. Select any different addresses in "select addresses" step and press button "Continue to shopping
     * information";</p>
     * <p>4. Select any shipping methods in shipping method tabs;</p>
     * <p>5. Check "Add gift options" checkbox;</p>
     * <p>6. Verify checkbox "Add Gift Card" is visible for order.</p>
     *
     * <p>Expected Results:</p>
     * <p>1. Gift Options block of Orders pages should contain information about Printed Card with the price that set
     * in the system configuration (Default Price for Printed Card).</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function withGiftCard($testData)
    {
        //Data
        $productsAdd = $this->loadDataSet('MultipleAddressesCheckout', 'products_to_add_with_qty', null,
            array('product_1' => $testData['simpleDefault'],
                  'product_2' => $testData['simpleDefault']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email'           => $testData['userDefault'],
                  'products_to_add' => $productsAdd), array('product_1'             => $testData['simpleDefault'],
                                                            'product_2'             => $testData['simpleDefault'],
                                                            'gift_options_address1' => array('add_printed_card' => 'Yes'),
                                                            'gift_options_address2' => array('add_printed_card' => 'Yes')));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage', 'gift_card_enable_yes'));
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Test Case TL-MAGE-994: Gift Receipt is allowed</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. In system configuration setting "Allow Gift Receipt" is set to "Yes";</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend;</p>
     * <p>2. Add any product to a Cart and proceed to checkout with Multiple Addresses Checkout;</p>
     * <p>3. Select any different addresses in "select addresses" step and press button "Continue to shopping
     * information";</p>
     * <p>4. Select any shipping methods in shipping method tabs;</p>
     * <p>5. Check "Add gift options" checkbox;</p>
     * <p>6. Verify checkbox "Add Gift Receipt" is visible for order.</p>
     *
     * <p>Expected Results:</p>
     * <p>1. Gift Options block of Order page should contain checked checkbox for Gift Receipt.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function giftReceiptYes($testData)
    {
        //Data
        $productsAdd = $this->loadDataSet('MultipleAddressesCheckout', 'products_to_add_with_qty', null,
            array('product_1' => $testData['simpleDefault'],
                  'product_2' => $testData['simpleDefault']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email'           => $testData['userDefault'],
                  'products_to_add' => $productsAdd), array('product_1'             => $testData['simpleDefault'],
                                                            'product_2'             => $testData['simpleDefault'],
                                                            'gift_options_address1' => array('send_gift_receipt' => 'Yes'),
                                                            'gift_options_address2' => array('send_gift_receipt' => 'Yes')));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage', 'gift_receipt_enable_yes'));
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Test Case TL-MAGE-860: Possibility to adding Gift attributes to Order during the process of Multiple Addresses
     * Checkout - Website</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. Website is created;</p>
     * <p>4. Gift wrapping is created for new website;</p>
     * <p>5. Gift wrapping and gift messages are allowed for entire order and individual items in system configuration
     * in website scope;</p>
     * <p>6. Navigate to newly created website URL.</p>
     * <p>Steps:</p>
     * <p>1. Log into Frontend;</p>
     * <p>2. Add one product to shopping cart;</p>
     * <p>3. Click "Checkout with Multiple Addresses" link;</p>
     * <p>4. Select a different shipping address for item;</p>
     * <p>5. Click button "Continue to shipping information";</p>
     * <p>6. Select "Flat Rate" shipping method for item;</p>
     * <p>7. Check "Add gift options" checkbox;</p>
     * <p>8. Check "Add Gift Options for Entire Order" checkbox;</p>
     * <p>9. Select Gift Wrapping from "Gift Wrapping Design" dropdown;</p>
     * <p>10. Click "Gift Message" link for entire order;</p>
     * <p>11. Add gift message for entire order;</p>
     * <p>12. Check "Add gift options for Individual Items" checkbox in the second item.
     * <p>13. Select Gift Wrapping from "Gift Wrapping Design" dropdown for item;</p>
     * <p>14. Click "Gift Message" link for individual item;</p>
     * <p>15. Add gift message for individual item;<p>
     * <p>16. Proceed to billing information page;</p>
     * <p>17. Select "Check/Money Order" payment method;</p>
     * <p>18. Proceed to review order information;</p>
     * <p>19. Check presence of gift wrapping for item and entire order in totals;</p>
     * <p>20. Submit order;</p>
     * <p>Expected Results:</p>
     * <p>1. Gift wrapping is mentioned in totals and its price is correct;</p>
     * <p>2. Order is created;</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function withGiftWrappingAndMessagePerWebsiteScope($testData)
    {
        //Data
        $wrappingWebsite = $this->loadDataSet('GiftMessage', 'gift_wrapping_all_enable_on_website',
            array('configuration_scope' => $testData['website']));
        $giftMessagesWebsite = $this->loadDataSet('GiftMessage', 'gift_message_all_enable_on_website',
            array('configuration_scope' => $testData['website']));
        $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'all_variants_for_gift_options',
            array('product_name'            => $testData['simple'],
                  'gift_wrapping_for_item'  => $testData['wrapping'],
                  'gift_wrapping_for_order' => $testData['wrapping']));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'all_variants_for_gift_options',
            array('product_name'            => $testData['simple'],
                  'gift_wrapping_for_item'  => $testData['wrapping'],
                  'gift_wrapping_for_order' => $testData['wrapping']));
        $productsAdd = $this->loadDataSet('MultipleAddressesCheckout', 'products_to_add_with_qty', null,
            array('product_1' => $testData['simple'],
                  'product_2' => $testData['simple']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email'           => $testData['user'],
                  'products_to_add' => $productsAdd), array('product_1'             => $testData['simple'],
                                                            'product_2'             => $testData['simple'],
                                                            'gift_options_address1' => $forProduct1,
                                                            'gift_options_address2' => $forProduct2));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($wrappingWebsite);
        $this->systemConfigurationHelper()->configure($giftMessagesWebsite);
        $newFrontendUrl = $this->stagingWebsiteHelper()->buildFrontendUrl($testData['code']);
        $this->_configHelper->setAreaBaseUrl('frontend', $newFrontendUrl);
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Test Case TL-MAGE-937: Recounting Gift Options (Entire Order)</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. Gift wrapping is created;</p>
     * <p>4. Gift wrapping and gift messages are allowed for entire order in system configuration.</p>
     * <p>Steps:</p>
     * <p>1. Log into Frontend;</p>
     * <p>2. Add one product to shopping cart;</p>
     * <p>3. Click on "Checkout with Multiple Addresses" link;</p>
     * <p>4. Select a different shipping address for item;</p>
     * <p>5. Click button "Continue to shipping information";</p>
     * <p>6. Select shipping method for item "Flat Rate";</p>
     * <p>7. Check the "Add gift options" checkbox;</p>
     * <p>8. Check the "Add Gift Options for Entire Order" checkbox;</p>
     * <p>9. Select Gift Wrapping from "Gift Wrapping Design" dropdown;</p>
     * <p>10. Click "Gift Message" link for entire order;</p>
     * <p>11. Add gift message for entire order;</p>
     * <p>12. Proceed to billing information page;</p>
     * <p>13. Select payment method "Check/Money Order";</p>
     * <p>14. Proceed to review order information;</p>
     * <p>15. Check presence of gift wrapping for entire order in totals;</p>
     * <p>16. Return back to shipping method selection;</p>
     * <p>17. Deselect gift options for order;</p>
     * <p>18. Repeat steps 12-14;</p>
     * <p>19. Check absence of gift wrapping for item and entire order in totals;</p>
     * <p>Expected Results:</p>
     * <p>1. Gift wrapping is absent in totals;</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function recountingGiftWrappingForOrder($testData)
    {
        //Data
        $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order');
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order');
        $reconfigureForProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order');
        $reconfigureForProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order');
        $productsAdd = $this->loadDataSet('MultipleAddressesCheckout', 'products_to_add_with_qty', null,
            array('product_1' => $testData['simpleDefault'],
                  'product_2' => $testData['simpleDefault']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email'           => $testData['userDefault'],
                  'products_to_add' => $productsAdd), array('product_1'             => $testData['simpleDefault'],
                                                            'product_2'             => $testData['simpleDefault'],
                                                            'gift_options_address1' => $forProduct1,
                                                            'gift_options_address2' => $forProduct2));
        $reconfigureShipping =
            $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login/shipping_data', null,
                array('product_1'             => $testData['simpleDefault'],
                      'product_2'             => $testData['simpleDefault'],
                      'gift_options_address1' => $reconfigureForProduct1,
                      'gift_options_address2' => $reconfigureForProduct2));
        $reconfiguredCheckout = $checkoutData;
        $reconfiguredCheckout['shipping_data'] = $reconfigureShipping;
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage',
            'order_gift_wrapping_no_message_yes'));
        $this->checkoutMultipleAddressesHelper()->doMultipleCheckoutSteps($checkoutData);
        $this->clickControl('link', 'shipping_method_change');
        $this->checkoutMultipleAddressesHelper()->verifyGiftOptions($checkoutData['shipping_data']);
        $this->checkoutMultipleAddressesHelper()->defineAndSelectShippingMethods($reconfigureShipping);
        $this->clickButton('continue_to_review_order');
        $this->checkoutMultipleAddressesHelper()->frontOrderReview($reconfiguredCheckout);
        $this->checkoutMultipleAddressesHelper()->placeMultipleCheckoutOrder();
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Test Case TL-MAGE-942: Recounting Gift Options (Individual Item)</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. Gift wrapping is created;</p>
     * <p>4. Gift wrapping and gift messages are allowed for individual items in system configuration.</p>
     * <p>Steps:</p>
     * <p>1. Log into Frontend;</p>
     * <p>2. Add one product to shopping cart;</p>
     * <p>3. Click on "Checkout with Multiple Addresses" link;</p>
     * <p>4. Select a different shipping address for item;</p>
     * <p>5. Click button "Continue to shipping information";</p>
     * <p>6. Select shipping method for item "Flat Rate";</p>
     * <p>7. Check the "Add gift options" checkbox;</p>
     * <p>8. Check the "Add Gift Options for Items" checkbox;</p>
     * <p>9. Select Gift Wrapping from "Gift Wrapping Design" dropdown;</p>
     * <p>10. Click "Gift Message" link for product;</p>
     * <p>11. Add gift message for item;</p>
     * <p>12. Proceed to billing information page;</p>
     * <p>13. Select payment method "Check/Money Order";</p>
     * <p>14. Proceed to review order information;</p>
     * <p>15. Check presence of gift wrapping for item and entire order in totals;</p>
     * <p>16. Return back to shipping method selection;</p>
     * <p>17. Deselect gift options for items;</p>
     * <p>18. Repeat steps 12-14;</p>
     * <p>19. Check absence of gift wrapping for item and entire order in totals;</p>
     * <p>Expected Results:</p>
     * <p>1. Gift wrapping is absent in totals;</p>
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
        $forProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_item',
            array('product_name'           => $testData['simpleDefault'],
                  'gift_wrapping_for_item' => $testData['wrappingDefault']));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_item',
            array('product_name'           => $testData['simpleDefault'],
                  'gift_wrapping_for_item' => $testData['wrappingDefault']));
        $reconfigureForProduct1 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_item',
            array('product_name'           => $testData['simpleDefault'],
                  'gift_wrapping_for_item' => $wrapping['gift_wrapping_design']));
        $reconfigureForProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_yes_item',
            array('product_name'           => $testData['simpleDefault'],
                  'gift_wrapping_for_item' => $wrapping['gift_wrapping_design']));
        $productsAdd = $this->loadDataSet('MultipleAddressesCheckout', 'products_to_add_with_qty', null,
            array('product_1' => $testData['simpleDefault'],
                  'product_2' => $testData['simpleDefault']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email'           => $testData['userDefault'],
                  'products_to_add' => $productsAdd), array('product_1'             => $testData['simpleDefault'],
                                                            'product_2'             => $testData['simpleDefault'],
                                                            'gift_options_address1' => $forProduct1,
                                                            'gift_options_address2' => $forProduct2));
        $reconfigureShipping =
            $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login/shipping_data', null,
                array('product_1'             => $testData['simpleDefault'],
                      'product_2'             => $testData['simpleDefault'],
                      'gift_options_address1' => $reconfigureForProduct1,
                      'gift_options_address2' => $reconfigureForProduct2));
        $reconfiguredCheckout = $checkoutData;
        $reconfiguredCheckout['shipping_data'] = $reconfigureShipping;
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage',
            'ind_items_gift_wrapping_yes_message_yes'));
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($wrapping);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->checkoutMultipleAddressesHelper()->doMultipleCheckoutSteps($checkoutData);
        $this->clickControl('link', 'shipping_method_change');
        $this->checkoutMultipleAddressesHelper()->verifyGiftOptions($checkoutData['shipping_data']);
        $this->checkoutMultipleAddressesHelper()->defineAndSelectShippingMethods($reconfigureShipping);
        $this->clickButton('continue_to_review_order');
        $this->checkoutMultipleAddressesHelper()->frontOrderReview($reconfiguredCheckout);
        $this->checkoutMultipleAddressesHelper()->placeMultipleCheckoutOrder();
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');

    }

    /**
     * <p>Test Case TL-MAGE-943: Recounting Gift Options (Printed Card to Order)</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>4. Printed card is allowed in system configuration.</p>
     * <p>Steps:</p>
     * <p>1. Log into Frontend;</p>
     * <p>2. Add one product to shopping cart;</p>
     * <p>3. Click on "Checkout with Multiple Addresses" link;</p>
     * <p>4. Select a different shipping address for item;</p>
     * <p>5. Click button "Continue to shipping information";</p>
     * <p>6. Select shipping method for item "Flat Rate";</p>
     * <p>7. Check the "Add gift options" checkbox;</p>
     * <p>8. Check the "Add Printed Card" checkbox;</p>
     * <p>9. Proceed to billing information page;</p>
     * <p>10. Select payment method "Check/Money Order";</p>
     * <p>11. Proceed to review order information;</p>
     * <p>12. Check presence of gift card in totals;</p>
     * <p>13. Return back to shipping method selection;</p>
     * <p>14. Deselect "Add printed card" checkbox;</p>
     * <p>18. Repeat steps 9-11;</p>
     * <p>19. Check absence of gift card in totals;</p>
     * <p>Expected Results:</p>
     * <p>1. Gift card price is absent in totals;</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     */
    public function recountingGiftCard($testData)
    {
        //Data
        $productsAdd = $this->loadDataSet('MultipleAddressesCheckout', 'products_to_add_with_qty', null,
            array('product_1' => $testData['simpleDefault'],
                  'product_2' => $testData['simpleDefault']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email'           => $testData['userDefault'],
                  'products_to_add' => $productsAdd), array('product_1' => $testData['simpleDefault'],
                                                            'product_2' => $testData['simpleDefault']));
        $reconfigureShipping =
            $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login/shipping_data', null,
                array('product_1'             => $testData['simpleDefault'],
                      'product_2'             => $testData['simpleDefault'],
                      'gift_options_address1' => array('add_printed_card' => 'Yes'),
                      'gift_options_address2' => array('add_printed_card' => 'Yes')));
        $reconfiguredCheckout = $checkoutData;
        $reconfiguredCheckout['shipping_data'] = $reconfigureShipping;
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage', 'gift_card_enable_yes'));
        $this->checkoutMultipleAddressesHelper()->doMultipleCheckoutSteps($checkoutData);
        $this->clickControl('link', 'shipping_method_change');
        $this->checkoutMultipleAddressesHelper()->verifyGiftOptions($checkoutData['shipping_data']);
        $this->checkoutMultipleAddressesHelper()->defineAndSelectShippingMethods($reconfigureShipping);
        $this->clickButton('continue_to_review_order');
        $this->checkoutMultipleAddressesHelper()->frontOrderReview($reconfiguredCheckout);
        $this->checkoutMultipleAddressesHelper()->placeMultipleCheckoutOrder();
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');

    }

    /**
     * <p>Test Case TL-MAGE-1039: No Gift Wrappings is created</p>
     * <p>Test Case TL-MAGE-858: Disabling Gift Wrapping (Multiple Addresses Checkout)</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>4. Gift Options are allowed in system configuration.</p>
     * <p>Steps:</p>
     * <p>1. Log into Frontend;</p>
     * <p>2. Add one product to shopping cart;</p>
     * <p>3. Click on "Checkout with Multiple Addresses" link;</p>
     * <p>4. Select a different shipping address for item;</p>
     * <p>5. Click button "Continue to shipping information";</p>
     * <p>6. Select shipping method for item "Flat Rate";</p>
     * <p>7. Check the "Add gift options" checkbox;</p>
     * <p>8. Check the "Add Gift Options for Entire Order" checkbox;</p>
     * <p>9. Verify there is not "Gift Wrapping Design" dropdown on the page.</p>
     * <p>Expected Results:</p>
     * <p>1. There is not "Gift Wrapping Design" dropdown on the page.</p>
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
            array('product_name' => $testData['simpleDefault']));
        $forProduct2 = $this->loadDataSet('MultipleAddressesCheckout', 'mess_yes_wrap_no_order_mess_yes_wrap_no_item',
            array('product_name' => $testData['simpleDefault']));
        $productsAdd = $this->loadDataSet('MultipleAddressesCheckout', 'products_to_add_with_qty', null,
            array('product_1' => $testData['simpleDefault'],
                  'product_2' => $testData['simpleDefault']));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_login',
            array('email'           => $testData['userDefault'],
                  'products_to_add' => $productsAdd), array('product_1'             => $testData['simpleDefault'],
                                                            'product_2'             => $testData['simpleDefault'],
                                                            'gift_options_address1' => $forProduct1,
                                                            'gift_options_address2' => $forProduct2));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage',
            'ind_items_all_yes_order_all_yes'));
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->disableAllGiftWrapping();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }
}
