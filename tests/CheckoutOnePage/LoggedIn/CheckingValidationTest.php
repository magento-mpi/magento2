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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page Checkout test
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CheckoutOnePage_LoggedIn_CheckingValidationTest extends Mage_Selenium_TestCase
{

    protected function assertPreConditions()
    {
        $this->addParameter('id', '');
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadData('simple_product_for_order');
        $userData = $this->loadData('generic_customer_account');
        //Steps and Verification
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array('sku' => $simple['general_name'],
            'customer' => array('email' => $userData['email'], 'password' => $userData['password']));
    }

    /**
     * <p>Empty required fields in billing address tab</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>2.Customer without address is registered.</p>
     * <p>3.Customer signed in at the frontend.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Proceed to Checkout".</p>
     * <p>4. Fill in Billing Information tab. Leave one required field empty</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Error message for field appears</p>
     *
     * @depends preconditionsForTests
     * @dataProvider addressEmptyFields
     * @test
     */
    public function emptyRequiredFildsInBillingAddress($field, $fieldType, $data)
    {
        //Preconditions
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        //Data
        $checkoutData = $this->loadData('empty_billing_address_fields',
                array('general_name' => $data['sku'], 'billing_' . $field => ''));
        //Steps
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        //Verification
        $this->addFieldIdToMessage($fieldType, 'billing_' . $field);
        if ($fieldType == 'dropdown') {
            $this->assertMessagePresent('validation', 'please_select_option');
        } else {
            $this->assertMessagePresent('validation', 'empty_required_field');
        }
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());;
    }

    /**
     * <p>Empty required fields in shipping address tab</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>2.Customer without address is registered.</p>
     * <p>3.Customer signed in at the frontend.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Proceed to Checkout".</p>
     * <p>4. Fill in Billing Information tab. Leave one required field empty</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Error message for field appears</p>
     *
     * @depends preconditionsForTests
     * @dataProvider addressEmptyFields
     * @test
     */
    public function emptyRequiredFildsInShippingAddress($field, $fieldType, $data)
    {
        //Preconditions
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        //Data
        $checkoutData = $this->loadData('empty_shipping_address_fields',
                array('general_name' => $data['sku'], 'shipping_' . $field => ''));
        //Steps
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        //Verification
        $this->addFieldIdToMessage($fieldType, 'shipping_' . $field);
        if ($fieldType == 'dropdown') {
            $this->assertMessagePresent('validation', 'please_select_option');
        } else {
            $this->assertMessagePresent('validation', 'empty_required_field');
        }
        $this->assertTrue($this->verifyMessagesCount(), $this->getParsedMessages());;
    }

    public function addressEmptyFields()
    {
        return array(
            array('first_name', 'field'),
            array('last_name', 'field'),
            array('street_address_1', 'field'),
            array('city', 'field'),
            array('state', 'dropdown'),
            array('zip_code', 'field'),
            array('country', 'dropdown'),
            array('telephone', 'field')
        );
    }

    /**
     * @depends preconditionsForTests
     * @dataProvider specialData
     * @test
     */
    public function specialValuesForAddressFields($dataName, $data)
    {
        //Data
        $checkoutData = $this->loadData($dataName, array('general_name' => $data['sku']));
        $userData = $this->loadData('customer_account_register');
        //Steps
        $this->logoutCustomer();
        $this->navigate('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_registration');
        //Steps
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    public function specialData()
    {
        return array(
            array('signedin_flatrate_checkmoney_long_address'),
            array('signedin_flatrate_checkmoney_special_address')
        );
    }

    /**
     * <p>Verifying "Use Billing Address" checkbox functionality</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in Billing Information tab</p>
     * <p>4. Select "Ship to different address" option</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>6. Check "Use Billing Address" checkbox</p>
     * <p>7. Verify data used for filling form</p>
     * <p>8. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Data must be the same as billing address</p>
     * <p>Customer successfully redirected to the next page, no error massages appears</p>
     *
     * @depends preconditionsForTests
     * @test
     */
    public function frontShippingAddressUseBillingAddress($data)
    {
        //Data
        $checkoutData = $this->loadData('signedin_flatrate_checkmoney_use_billing_in_shipping',
                array('general_name' => $data['sku']));
        $userData = $this->loadData('customer_account_register');
        //Steps
        $this->logoutCustomer();
        $this->navigate('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_registration');
        //Steps
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * <p>Shipping method not defined</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in Billing Information tab</p>
     * <p>4. Select "Ship to this address" option</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>6. Leave Shipping Method options empty</p>
     * <p>7. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Information window appears "Please specify shipping method."</p>
     *
     * @depends preconditionsForTests
     * @test
     */
    public function shippingMethodNotDefined($data)
    {
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('free_enable');
        //Data
        $checkoutData = $this->loadData('empty_shipping_address_fields', array('general_name' => $data['sku']));
        $userData = $this->loadData('customer_account_register');
        //Steps
        $this->logoutCustomer();
        $this->navigate('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_registration');
        //Steps
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
        $this->clickButton('shipping_method_continue', false);
        $this->waitForAjax();
        if ($this->isAlertPresent()) {
            $text = $this->getAlert();
            $this->assertEquals($text, 'Please specify shipping method.');
        }
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('shipping_method');
    }

    /**
     * <p>Payment method not defined</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in Billing Information tab</p>
     * <p>4. Select "Ship to this address" option</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>6. Select Shipping Method option</p>
     * <p>7. Click 'Continue' button.</p>
     * <p>8. Leave Payment Method options empty</p>
     * <p>9. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Information window appears "Please specify payment method."</p>
     *
     * @depends preconditionsForTests
     * @test
     */
    public function frontPaymentMethodNotDefined($data)
    {
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('savedcc_without_3Dsecure');
        //Data
        $checkoutData = $this->loadData('empty_payment_method', array('general_name' => $data['sku']));
        $userData = $this->loadData('customer_account_register');
        //Steps
        $this->logoutCustomer();
        $this->navigate('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        //Verifying
        $this->assertMessagePresent('success', 'success_registration');
        //Steps
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('payment_method');
        $this->clickButton('payment_method_continue', false);
        $this->waitForAjax();
        if ($this->isAlertPresent()) {
            $text = $this->getAlert();
            $this->assertEquals($text, 'Please specify payment method.');
        }
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('payment_method');
    }

}
