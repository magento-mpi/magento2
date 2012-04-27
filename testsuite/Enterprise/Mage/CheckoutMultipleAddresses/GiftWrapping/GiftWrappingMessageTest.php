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
class Enterprise_Mage_CheckoutMultipleAddresses_GiftWrapping_GiftWrappingMessageTest extends Mage_Selenium_TestCase
{
    public function assertPreconditions()
    {
        $this->loginAdminUser();
        $this->_applicationHelper = $this->_testConfig->getApplicationHelper();
        $this->_applicationHelper->changeApplication('magento');
    }

    protected function tearDownAfterTest()
    {
        $this->_applicationHelper = $this->_testConfig->getApplicationHelper();
        $this->_applicationHelper->changeApplication('magento');
    }

    /**
     * <p>Preconditions:</p>
     * <p>Create Staging Website</p>
     *
     * @return array $website
     *
     * @test
     */
    public function createWebsite()
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('staging_website_enable_auto_entries');
        //Data
        $website = $this->loadData('staging_website');
        $this->_applicationHelper = $this->_testConfig->getApplicationHelper();
        //Steps
        $this->navigate('manage_staging_websites');
        $this->stagingWebsiteHelper()->createStagingWebsite($website);
        //Verification
        $this->assertMessagePresent('success', 'success_created_website');

        return $website;
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @return array $productData
     *
     * @test
     */
    public function preconditionsCreateProduct()
    {
        //Data
        $productData = $this->loadData('simple_product_visible');
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @depends createWebsite
     * @param array $website
     * @return array $productData
     *
     * @test
     *
     */
    public function preconditionsCreateProductForWebsite($website)
    {
        //Data
        $productData = $this->loadData('simple_product_visible',
            array('websites' => $website['general_information']['staging_website_name']));
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        return $productData;
    }

    /**
     * <p>Create Customer</p>
     *
     * @return array $userData
     * @test
     */
    public function preconditionsCreateCustomer()
    {
        //Data
        $userData = $this->loadData('customer_account_register');
        //Steps
        $this->logoutCustomer();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        //Verification
        $this->assertMessagePresent('success', 'success_registration');
        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * <p>Create Customer</p>
     *
     * @depends createWebsite
     * @param array $website
     * @return array $userData
     *
     * @test
     */
    public function preconditionsCreateCustomerForWebsite($website)
    {
        //Data
        $userData = $this->loadData('customer_account_register');
        //Steps
        $this->_applicationHelper = $this->_testConfig->getApplicationHelper();
        $newConfig = $this->stagingWebsiteHelper()->newConfigDataForStaging(
                    $website['general_information']['staging_website_code']);
        $this->_applicationHelper->changeApplication($website['general_information']['staging_website_code'],
                                                             $newConfig);
        $this->logoutCustomer();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        //Verification
        $this->assertMessagePresent('success', 'success_registration');
        return array('email' => $userData['email'], 'password' => $userData['password']);
    }

    /**
     * <p>Test cases: TL-MAGE-965 and TL-MAGE-961 and TL-MAGE-853</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. Gift wrapping is created;</p>
     * <p>4. Gift wrapping and gift messages are allowed for entire order and individual items in system configuration.</p>
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
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function checkoutWithGiftWrappingAndMessage($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_wrapping_all_enable');
        $this->systemConfigurationHelper()->configure('gift_message_all_enable');
        //Data
        $giftWrappingData = $this->loadData('gift_wrapping_without_image');
        $individualItemsMessage = $this->loadData('gift_message_for_individual_items');
        $indItems = array($productData['general_name'] =>
                                array('item_gift_wrapping_design'  => $giftWrappingData['gift_wrapping_design'],
                                      'gift_message'               => $individualItemsMessage));
        $checkoutData = $this->loadData('multiple_with_gift_wrapping_and_message',
                                array('email'                      => $customerData['email'],
                                      'password'                   => $customerData['password'],
                                      'order_gift_wrapping_design' => $giftWrappingData['gift_wrapping_design'],
                                      'individual_items'           => $indItems,
                                      'product_name'               => $productData['general_name'] .
                                      ' Gift Wrapping Design : ' . $giftWrappingData['gift_wrapping_design']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData);
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
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function giftWrappingAndMessagesForEntireOrderOnly($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('order_gift_wrapping_yes_message_yes');
        //Data
        $giftWrappingData = $this->loadData('gift_wrapping_without_image');
        $checkoutData = $this->loadData('multiple_with_gift_wrapping_and_message_for_order_only',
                                array('email'       => $customerData['email'],
                                      'password'    => $customerData['password']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        $shippingInfoData = $this->loadData('shipping_data_for_wrapping');
        $shippingInfoData = $this->arrayEmptyClear($shippingInfoData);
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingInfoData, false);
        $this->fillForm(array('add_gift_options'                 => 'Yes',
                              'gift_option_for_order'            => 'Yes'));
        //Verification
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertTrue($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'order_gift_wrapping_design')),
                        'Gift wrapping for entire order is not available');
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('link', 'gift_message_for_order')),
                        'Gift message for entire order is not available');
        $this->assertFalse($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('checkbox', 'gift_option_for_item')),
                        'Gift options checkbox for individual items is available');
        $this->assertFalse($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'item_gift_wrapping_design')),
                        'Gift wrapping for individual item is available');
        $this->assertFalse($this->isElementPresent(
                $this->_getControlXpath('link', 'gift_message_for_item')),
                        'Gift message for individual item is available');
    }

    /**
     * <p>Test case:</p>
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
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function giftWrappingAndMessagesForIndItemsOnly($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ind_items_gift_wrapping_yes_message_yes');
        //Data
        $giftWrappingData = $this->loadData('gift_wrapping_without_image');
        $checkoutData = $this->loadData('multiple_with_gift_wrapping_and_message_for_order_only',
                                array('email'       => $customerData['email'],
                                      'password'    => $customerData['password']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        $shippingInfoData = $this->loadData('shipping_data_for_wrapping');
        $shippingInfoData = $this->arrayEmptyClear($shippingInfoData);
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingInfoData, false);
        $this->fillForm(array('add_gift_options'                 => 'Yes',
                              'gift_option_for_item'             => 'Yes'));
        //Verification
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertFalse($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('checkbox', 'gift_option_for_order')),
                        'Gift options checkbox for entire order is available');
        $this->assertFalse($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'order_gift_wrapping_design')),
                        'Gift wrapping for entire order is available');
        $this->assertFalse($this->isElementPresent(
                $this->_getControlXpath('link', 'gift_message_for_order')),
                        'Gift message for entire order is available');
        $this->assertTrue($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'item_gift_wrapping_design')),
                        'Gift wrapping for individual item is not available');
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('link', 'gift_message_for_item')),
                        'Gift message for individual item is not available');
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
     * <p>3. Select any different addresses in "select addresses" step and press button "Continue to shopping information";</p>
     * <p>4. Select any shipping methods in shipping method tabs;</p>
     * <p>5. Check the "Add gift options" checkboxes;</p>
     * <p>6. Check "Add gift options for Individual Items" checkboxes;</p>
     *
     * <p>Expected Results:</p>
     * <p>1. Dropdown "Gift Wrapping Design" for individual items should not be shown.</p>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function giftWrappingForIndItemsNoAndMessagesYes($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ind_items_gift_wrapping_no_message_yes');
        //Data
        $giftWrappingData = $this->loadData('gift_wrapping_without_image');
        $checkoutData = $this->loadData('multiple_with_gift_wrapping_and_message_for_order_only',
                                array('email'       => $customerData['email'],
                                      'password'    => $customerData['password']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        $shippingInfoData = $this->loadData('shipping_data_for_wrapping');
        $shippingInfoData = $this->arrayEmptyClear($shippingInfoData);
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingInfoData, false);
        $this->fillForm(array('add_gift_options'                 => 'Yes',
                              'gift_option_for_item'             => 'Yes'));
        //Verification
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('link', 'gift_message_for_item')),
                        'Gift message for individual item is available');
        $this->assertFalse($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'item_gift_wrapping_design')),
                        'Gift wrapping for individual item is available');
    }

    /**
     * <p>TL-MAGE-970:Gift Wrapping for Individual Items is not allowed (wrapping-no; message-no)</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. Gift wrapping is created;</p>
     * <p>4. In system configuration setting "Allow Gift Messages for Individual Items" is set to "No";</p>
     * <p>5. In system configuration setting "Allow Gift Wrapping for Individual Items" is set to "No".</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend;</p>
     * <p>2. Add any product to a Cart and proceed to checkout with Multiple Addresses Checkout;</p>
     * <p>3. Select any different addresses in "select addresses" step and press button "Continue to shopping information";</p>
     * <p>4. Select any shipping methods in shipping method tabs;</p>
     * <p>5. Check "Add gift options" checkbox;</p>
     *
     * <p>Expected Results:</p>
     * <p>1. "Add gift options for Individual Items" checkbox should not be shown.</p>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function giftWrappingForIndItemsNoAndMessagesNo($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ind_items_gift_wrapping_no_message_no');
        //Data
        $giftWrappingData = $this->loadData('gift_wrapping_without_image');
        $checkoutData = $this->loadData('multiple_with_gift_wrapping_and_message_for_order_only',
                                array('email'       => $customerData['email'],
                                      'password'    => $customerData['password']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        $shippingInfoData = $this->loadData('shipping_data_for_wrapping');
        $shippingInfoData = $this->arrayEmptyClear($shippingInfoData);
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingInfoData, false);
        $this->fillForm(array('add_gift_options' => 'Yes'));
        //Verification
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertFalse($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('checkbox', 'gift_option_for_item')),
                        'Gift options for individual item are available');
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
     * <p>3. Select any different addresses in "select addresses" step and press button "Continue to shopping information";</p>
     * <p>4. Select any shipping methods in shipping method tabs;</p>
     * <p>5. Check the "Add gift options" checkbox;</p>
     *
     * <p>Expected Results:</p>
     * <p>1. "Add gift options for Individual Items" checkbox should not be shown.</p>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function giftWrappingForIndItemsYesAndMessagesNo($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ind_items_gift_wrapping_yes_message_no');
        //Data
        $giftWrappingData = $this->loadData('gift_wrapping_without_image');
        $checkoutData = $this->loadData('multiple_with_gift_wrapping_and_message_for_order_only',
                                array('email'       => $customerData['email'],
                                      'password'    => $customerData['password']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        $shippingInfoData = $this->loadData('shipping_data_for_wrapping');
        $shippingInfoData = $this->arrayEmptyClear($shippingInfoData);
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingInfoData, false);
        $this->fillForm(array('add_gift_options'                 => 'Yes',
                              'gift_option_for_item'             => 'Yes'));
        //Verification
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertFalse($this->isElementPresent(
                $this->_getControlXpath('link', 'gift_message_for_item')),
                        'Gift message for individual item is available');
        $this->assertTrue($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'item_gift_wrapping_design')),
                        'Gift wrapping for individual item is available');
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
     * <p>3. Select any different addresses in "select addresses" step and press button "Continue to shopping information";</p>
     * <p>4. Select any shipping methods in shipping method tabs;</p>
     * <p>5. Check "Add gift options" checkbox;</p>
     * <p>6. Verify dropdown "Gift Wrapping Design" is not visible for order;</p>
     * <p>7. Verify link "Gift Message" is visible for order;</p>
     *
     * <p>Expected Results:</p>
     * <p>1. "Gift Wrapping Design" dropdown is not visible for order;</p>
     * <p>2. "Gift Message" link is visible for order.</p>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function giftWrappingForOrderNoAndMessagesYes($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('order_gift_wrapping_no_message_yes');
        //Data
        $giftWrappingData = $this->loadData('gift_wrapping_without_image');
        $checkoutData = $this->loadData('multiple_with_gift_wrapping_and_message_for_order_only',
                                array('email'       => $customerData['email'],
                                      'password'    => $customerData['password']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        $shippingInfoData = $this->loadData('shipping_data_for_wrapping');
        $shippingInfoData = $this->arrayEmptyClear($shippingInfoData);
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingInfoData, false);
        $this->fillForm(array('add_gift_options'      => 'Yes',
                              'gift_option_for_order' => 'Yes'));
        //Verification
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertTrue($this->isElementPresent(
                $this->_getControlXpath('link', 'gift_message_for_order')),
                        'Gift message for order is not available');
        $this->assertFalse($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'order_gift_wrapping_design')),
                        'Gift wrapping for order is available');
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
     * <p>3. Select any different addresses in "select addresses" step and press button "Continue to shopping information";</p>
     * <p>4. Select any shipping methods in shipping method tabs;</p>
     * <p>5. Check the "Add gift options" checkbox;</p>
     * <p>6. Verify dropdown "Gift Wrapping Design" is visible for order;</p>
     * <p>7. Verify link "Gift Message" is not visible for order;</p>
     *
     * <p>Expected Results:</p>
     * <p>1. "Gift Wrapping Design" dropdown is visible for order;</p>
     * <p>2. "Gift Message" link is not visible for order.</p>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function giftWrappingForOrderYesAndMessagesNo($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('order_gift_wrapping_yes_message_no');
        //Data
        $giftWrappingData = $this->loadData('gift_wrapping_without_image');
        $checkoutData = $this->loadData('multiple_with_gift_wrapping_and_message_for_order_only',
                                array('email'       => $customerData['email'],
                                      'password'    => $customerData['password']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        $shippingInfoData = $this->loadData('shipping_data_for_wrapping');
        $shippingInfoData = $this->arrayEmptyClear($shippingInfoData);
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingInfoData, false);
        $this->fillForm(array('add_gift_options'      => 'Yes',
                              'gift_option_for_order' => 'Yes'));
        //Verification
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertFalse($this->isElementPresent(
                $this->_getControlXpath('link', 'gift_message_for_order')),
                        'Gift message for order is available');
        $this->assertTrue($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'order_gift_wrapping_design')),
                        'Gift wrapping for order is not available');
    }

    /**
     * <p>TL-MAGE-962:Gift Wrapping for entire Order is not allowed (wrapping-no; message-no)</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. Gift wrapping is created;</p>
     * <p>4. In system configuration setting "Allow Gift Messages On Order Level" is set to "No";</p>
     * <p>5. In system configuration setting "Allow Gift Wrapping On Order Level" is set to "No".</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend;</p>
     * <p>2. Add any product to a Cart and proceed to checkout with Multiple Addresses Checkout;</p>
     * <p>3. Select any different addresses in "select addresses" step and press button "Continue to shopping information";</p>
     * <p>4. Select any shipping methods in shipping method tabs;</p>
     * <p>5. Check "Add gift options" checkbox;</p>
     * <p>6. Verify checkbox "Add Gift Options For The Entire Order" is not visible for order.</p>
     *
     * <p>Expected Results:</p>
     * <p>1. Checkbox "Add Gift Options For The Entire Order" is not visible for order.</p>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function giftWrappingForOrderNoAndMessagesNo($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('order_gift_wrapping_no_message_no');
        //Data
        $giftWrappingData = $this->loadData('gift_wrapping_without_image');
        $checkoutData = $this->loadData('multiple_with_gift_wrapping_and_message_for_order_only',
                                array('email'       => $customerData['email'],
                                      'password'    => $customerData['password']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        $shippingInfoData = $this->loadData('shipping_data_for_wrapping');
        $shippingInfoData = $this->arrayEmptyClear($shippingInfoData);
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingInfoData, false);
        $this->fillForm(array('add_gift_options'     => 'Yes'));
        //Verification
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertFalse($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('checkbox', 'gift_option_for_order')),
                        'Add gift options for order checkbox is available');
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
     * <p>3. Select any different addresses in "select addresses" step and press button "Continue to shopping information";</p>
     * <p>4. Select any shipping methods in shipping method tabs;</p>
     * <p>5. Check "Add gift options" checkbox;</p>
     * <p>6. Verify checkbox "Add Gift Card" is visible for order.</p>
     *
     * <p>Expected Results:</p>
     * <p>1. Gift Options block of Orders pages should contain information about Printed Card with the price that set in the system configuration (Default Price for Printed Card).</p>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function giftCardYes($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_card_enable_yes');
        //Data
        $checkoutData = $this->loadData('multiple_with_gift_card',
                                array('email'        => $customerData['email'],
                                      'password'     => $customerData['password'],
                                      'product_name' => $productData['general_name']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        //Steps
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Test Case TL-MAGE-993: Printed Card to Order is not allowed</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. In system configuration setting "Allow Printed Card" is set to "No";</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend;</p>
     * <p>2. Add any product to a Cart and proceed to checkout with Multiple Addresses Checkout;</p>
     * <p>3. Select any different addresses in "select addresses" step and press button "Continue to shopping information";</p>
     * <p>4. Select any shipping methods in shipping method tabs;</p>
     * <p>5. Check "Add gift options" checkbox;</p>
     * <p>6. Verify checkbox "Add Gift Card" is not visible for order.</p>
     *
     * <p>Expected Results:</p>
     * <p>1. "Add Printed Card" checkbox should not be visible.</p>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function giftCardNo($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_card_enable_no');
        //Data
        $checkoutData = $this->loadData('multiple_with_gift_wrapping_and_message_for_order_only',
                                array('email'       => $customerData['email'],
                                      'password'    => $customerData['password']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        $shippingInfoData = $this->loadData('shipping_data_for_wrapping');
        $shippingInfoData = $this->arrayEmptyClear($shippingInfoData);
        //Steps
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingInfoData, false);
        $this->fillForm(array('add_gift_options'     => 'Yes'));
        //Verification
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertFalse($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('checkbox', 'add_printed_card')),
                        'Add printed card is available');
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
     * <p>3. Select any different addresses in "select addresses" step and press button "Continue to shopping information";</p>
     * <p>4. Select any shipping methods in shipping method tabs;</p>
     * <p>5. Check "Add gift options" checkbox;</p>
     * <p>6. Verify checkbox "Add Gift Receipt" is visible for order.</p>
     *
     * <p>Expected Results:</p>
     * <p>1. Gift Options block of Order page should contain checked checkbox for Gift Receipt.</p>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function giftReceiptYes($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_receipt_enable_yes');
        //Data
        $checkoutData = $this->loadData('multiple_with_gift_receipt',
                                array('email'        => $customerData['email'],
                                      'password'     => $customerData['password'],
                                      'product_name' => $productData['general_name']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        //Steps
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Test Case TL-MAGE-995: Gift Receipt is not allowed</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. In system configuration setting "Allow Gift Receipt" is set to "No";</p>
     * <p>Steps:</p>
     * <p>1. Log in to frontend;</p>
     * <p>2. Add any product to a Cart and proceed to checkout with Multiple Addresses Checkout;</p>
     * <p>3. Select any different addresses in "select addresses" step and press button "Continue to shopping information";</p>
     * <p>4. Select any shipping methods in shipping method tabs;</p>
     * <p>5. Check "Add gift options" checkbox;</p>
     * <p>6. Verify checkbox "Add Gift Receipt" is not visible for order.</p>
     *
     * <p>Expected Results:</p>
     * <p>1. "Add Gift Receipt" checkbox should not be visible.</p>
     *
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function giftReceiptNo($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_receipt_enable_no');
        //Data
        $checkoutData = $this->loadData('multiple_with_gift_wrapping_and_message_for_order_only',
                                array('email'       => $customerData['email'],
                                      'password'    => $customerData['password']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        $shippingInfoData = $this->loadData('shipping_data_for_wrapping');
        $shippingInfoData = $this->arrayEmptyClear($shippingInfoData);
        //Steps
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingInfoData, false);
        $this->fillForm(array('add_gift_options'     => 'Yes'));
        //Verification
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertFalse($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('checkbox', 'send_gift_receipt')),
                        'Send Gift Receipt is available');
    }

    /**
     * <p>Test Case TL-MAGE-860: Possibility to adding Gift attributes to Order during the process of Multiple Addresses Checkout - Website</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. Website is created;</p>
     * <p>4. Gift wrapping is created for new website;</p>
     * <p>5. Gift wrapping and gift messages are allowed for entire order and individual items in system configuration in website scope;</p>
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
     * @depends preconditionsCreateCustomerForWebsite
     * @depends preconditionsCreateProductForWebsite
     * @depends createWebsite
     * @param array $customerData
     * @param array $productData
     * @param array $website
     *
     * @test
     */
    public function checkoutWithGiftWrappingAndMessageWebsiteScope($customerData, $productData, $website)
    {
        //Preconditions
        $giftWrappingEnableWebsite = $this->loadData('gift_wrapping_all_enable_on_website',
            array('configuration_scope' => $website['general_information']['staging_website_name']));
        $giftMessagesEnableWebsite = $this->loadData('gift_message_all_enable_on_website',
            array('configuration_scope' => $website['general_information']['staging_website_name']));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_message_all_disable');
        $this->systemConfigurationHelper()->configure('gift_wrapping_all_disable');
        $this->systemConfigurationHelper()->configure($giftWrappingEnableWebsite);
        $this->systemConfigurationHelper()->configure($giftMessagesEnableWebsite);
        //Data
        $giftWrappingData = $this->loadData('gift_wrapping_without_image',
            array('gift_wrapping_websites' => $website['general_information']['staging_website_name']));
        $individualItemsMessage = $this->loadData('gift_message_for_individual_items');
        $indItems = array($productData['general_name'] =>
                                array('item_gift_wrapping_design'  => $giftWrappingData['gift_wrapping_design'],
                                      'gift_message'               => $individualItemsMessage));
        $checkoutData = $this->loadData('multiple_with_gift_wrapping_and_message',
                                array('email'                      => $customerData['email'],
                                      'password'                   => $customerData['password'],
                                      'order_gift_wrapping_design' => $giftWrappingData['gift_wrapping_design'],
                                      'individual_items'           => $indItems,
                                      'product_name'               => $productData['general_name'] .
                                      ' Gift Wrapping Design : ' . $giftWrappingData['gift_wrapping_design']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->_applicationHelper = $this->_testConfig->getApplicationHelper();
        $newConfig = $this->stagingWebsiteHelper()->newConfigDataForStaging(
                            $website['general_information']['staging_website_code']);
        $this->_applicationHelper->changeApplication($website['general_information']['staging_website_code'],
            $newConfig);
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->addParameter('param', '?no_cache=');
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData);
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
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function recountingGiftWrappingForOrder($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_wrapping_all_enable');
        $this->systemConfigurationHelper()->configure('gift_message_all_enable');
        //Data
        $giftWrappingData = $this->loadData('gift_wrapping_without_image');
        $checkoutData = $this->loadData('recount_gift_options_for_order_checkout',
                                array('email'                      => $customerData['email'],
                                      'password'                   => $customerData['password'],
                                      'order_gift_wrapping_design' => $giftWrappingData['gift_wrapping_design'],
                                      'product_name'               => $productData['general_name']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData, false);
        //Verification
        $this->checkoutMultipleAddressesHelper()->frontOrderReview($checkoutData);
        //Steps
        $this->clickControl('link', 'shipping_method_change');
        $this->fillForm(array('add_gift_options' => 'Yes', 'gift_option_for_order' => 'No'));
        $this->clickButton('continue_to_billing_information');
        $this->clickButton('continue_to_review_order');
        $recountCheckout = $this->loadData('recount_gift_options_for_order',
            array('product_name' => $productData['general_name']));
        //Verification
        $this->checkoutMultipleAddressesHelper()->frontOrderReview($recountCheckout);
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
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function recountingGiftWrappingForItems($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_wrapping_all_enable');
        $this->systemConfigurationHelper()->configure('gift_message_all_enable');
        //Data
        $giftWrappingData = $this->loadData('gift_wrapping_without_image');
        $individualItemsMessage = $this->loadData('gift_message_for_individual_items');
        $indItems = array($productData['general_name'] =>
                                array('item_gift_wrapping_design'  => $giftWrappingData['gift_wrapping_design'],
                                      'gift_message'               => $individualItemsMessage));
        $checkoutData = $this->loadData('recount_gift_options_for_items_checkout',
                                array('email'                      => $customerData['email'],
                                      'password'                   => $customerData['password'],
                                      'order_gift_wrapping_design' => $giftWrappingData['gift_wrapping_design'],
                                      'individual_items'           => $indItems,
                                      'product_name'               => $productData['general_name'] .
                                      ' Gift Wrapping Design : ' . $giftWrappingData['gift_wrapping_design']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData, false);
        //Verification
        $this->checkoutMultipleAddressesHelper()->frontOrderReview($checkoutData);
        //Steps
        $this->clickControl('link', 'shipping_method_change');
        $this->fillForm(array('add_gift_options' => 'Yes', 'gift_option_for_item' => 'No'));
        $this->clickButton('continue_to_billing_information');
        $this->clickButton('continue_to_review_order');
        $recountCheckout = $this->loadData('recount_gift_options_for_order',
            array('product_name' => $productData['general_name']));
        $this->checkoutMultipleAddressesHelper()->frontOrderReview($recountCheckout);
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
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function recountingGiftCard($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_wrapping_all_enable');
        $this->systemConfigurationHelper()->configure('gift_message_all_enable');
        //Data
        $checkoutData = $this->loadData('recount_gift_card_checkout',
                                array('email'                      => $customerData['email'],
                                      'password'                   => $customerData['password'],
                                      'product_name'               => $productData['general_name']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        //Steps
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData, false);
        //Verification
        $this->checkoutMultipleAddressesHelper()->frontOrderReview($checkoutData);
        //Steps
        $this->clickControl('link', 'shipping_method_change');
        $this->fillForm(array('add_gift_options' => 'Yes', 'add_printed_card' => 'No'));
        $this->clickButton('continue_to_billing_information');
        $this->clickButton('continue_to_review_order');
        $recountCheckout = $this->loadData('recount_gift_options_for_order',
            array('product_name' => $productData['general_name']));
        $this->checkoutMultipleAddressesHelper()->frontOrderReview($recountCheckout);
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
     * @depends preconditionsCreateCustomer
     * @depends preconditionsCreateProduct
     * @param array $customerData
     * @param array $productData
     *
     * @test
     */
    public function checkoutWithoutGiftWrapping($customerData, $productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_wrapping_all_enable');
        $this->systemConfigurationHelper()->configure('gift_message_all_enable');
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->disableAllGiftWrapping();
        //Data
        $checkoutData = $this->loadData('miltiple_without_gift_wrapping',
                                array('email'                      => $customerData['email'],
                                      'password'                   => $customerData['password'],
                                      'product_name'               => $productData['general_name']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        $shippingData = $checkoutData['shipping_data'];
        $shippingData = $this->arrayEmptyClear($shippingData);
        unset($checkoutData['shipping_data']);
        //Steps
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingData, false);
        $this->fillForm(array('add_gift_options'      => 'Yes',
                              'gift_option_for_order' => 'Yes',
                              'gift_option_for_item'  => 'Yes'));
        //Verification
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertFalse($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'order_gift_wrapping_design')),
                        'Gift Wrapping Design dropdown is available');
        $this->assertFalse($this->isElementPresent($fieldsetXpath .
                $this->_getControlXpath('dropdown', 'item_gift_wrapping_design')),
                        'Gift Wrapping Design dropdown is available');
    }

    /**
     * <p>Test Case TL-MAGE-871: Possibility to adding Gift attributes to Order during the process of Multiple Addresses Checkout - Global</p>
     * <p>Preconditions:</p>
     * <p>1. Simple product is created;</p>
     * <p>2. Customer is created;</p>
     * <p>3. Website is created;</p>
     * <p>4. Gift wrapping is created for new website;</p>
     * <p>5. Gift wrapping and gift messages are allowed for entire order and individual items in system configuration in global scope;</p>
     * <p>6. Gift wrapping and gift messages are not allowed for entire order and individual items in system configuration in website scope;</p>
     * <p>7. Navigate to newly created website URL.</p>
     * <p>Steps:</p>
     * <p>1. Log into Frontend;</p>
     * <p>2. Add one product to shopping cart;</p>
     * <p>3. Click on "Checkout with Multiple Addresses" link;</p>
     * <p>4. Select a different shipping address for item;</p>
     * <p>5. Click button "Continue to shipping information";</p>
     * <p>6. Select shipping method for item "Flat Rate";</p>
     * <p>7. Check the "Add gift options" checkbox;</p>
     * <p>Expected Results:</p>
     * <p>1. "Add gift options" checkbox is not visible;</p>
     *
     * @depends preconditionsCreateCustomerForWebsite
     * @depends preconditionsCreateProductForWebsite
     * @depends createWebsite
     * @param array $customerData
     * @param array $productData
     * @param array $website
     *
     * @test
     */
    public function checkoutWithGiftWrappingAndMessageGlobalScope($customerData, $productData, $website)
    {
        //Preconditions
        $giftWrappingDisableWebsite = $this->loadData('gift_wrapping_all_disable_on_website',
            array('configuration_scope' => $website['general_information']['staging_website_name']));
        $giftMessagesDisableWebsite = $this->loadData('gift_message_all_disable_on_website',
            array('configuration_scope' => $website['general_information']['staging_website_name']));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_message_all_enable');
        $this->systemConfigurationHelper()->configure('gift_wrapping_all_enable');
        $this->systemConfigurationHelper()->configure($giftWrappingDisableWebsite);
        $this->systemConfigurationHelper()->configure($giftMessagesDisableWebsite);
        //Data
        $giftWrappingData = $this->loadData('gift_wrapping_without_image',
            array('gift_wrapping_websites' => $website['general_information']['staging_website_name']));
        $checkoutData = $this->loadData('multiple_with_gift_wrapping_and_message_for_order_only',
                                array('email'                      => $customerData['email'],
                                      'password'                   => $customerData['password']));
        $checkoutData['shipping_address_data']['address_to_ship_1']['general_name'] = $productData['general_name'];
        $checkoutData['products_to_add']['product_1']['general_name'] = $productData['general_name'];
        $shippingInfoData = $this->loadData('shipping_data_for_wrapping');
        $shippingInfoData = $this->arrayEmptyClear($shippingInfoData);
        //Steps
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        $this->_applicationHelper = $this->_testConfig->getApplicationHelper();
        $newConfig = $this->stagingWebsiteHelper()->newConfigDataForStaging(
                            $website['general_information']['staging_website_code']);
        $this->_applicationHelper->changeApplication($website['general_information']['staging_website_code'],
            $newConfig);
        $this->customerHelper()->frontLoginCustomer($customerData);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
        $this->addParameter('param', '?no_cache=');
        $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData, false);
        $this->checkoutMultipleAddressesHelper()->fillShippingInfo($shippingInfoData, false);
        //Verification
        $fieldsetXpath = $this->_getControlXpath('fieldset', 'shipping_method');
        $this->assertFalse($this->isVisible($fieldsetXpath .
                $this->_getControlXpath('checkbox', 'add_gift_options')),
                        'Gift options checkbox is available');
    }
}
