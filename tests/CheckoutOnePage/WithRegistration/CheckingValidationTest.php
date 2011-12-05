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
 * One page Checkout  - checking validation test
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CheckoutOnePage_WithRegistration_CheckingValidationTest extends Mage_Selenium_TestCase
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
        //Steps and Verification
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('shipping_disable');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);

        return $simple['general_name'];
    }

    /**
     * <p>Product not defined to shopping cart</p>
     * <p>Steps</p>
     * <p>1. Do not add product to shopping Cart</p>
     * <p>2. Click "Checkout" button</p>
     * <p>Expected Result</p>
     * <p>Shopping Cart is Empty page appears</p>
     *
     * @test
     */
    public function frontEmptyShoppingCart()
    {
        //Steps
        $this->logoutCustomer();
        $this->clickControl('link', 'checkout');
        $this->validatePage('shopping_cart');
        //Verifying
        $this->assertElementPresent($this->_getControlXpath('message', 'shopping_cart_is_empty'),
                'Shopping cart is not empty');
    }

    /**
     * <p>Checkout method is not defined</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Leave Checkout Method options empty</p>
     * <p>2. Click "Continue" button</p>
     * <p>Expected Result</p>
     * <p>Information window appears with message "Please choose to register or to checkout as a guest"</p>
     *
     * @depends preconditionsForTests
     * @test
     */
    public function frontCheckoutMethodNotDefined($simpleSku)
    {
        //Data
        $checkoutData = $this->loadData('empty_checkout_data', array('general_name' => $simpleSku));
        //Steps
        $this->logoutCustomer();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('checkout_method');
        $this->clickButton('checkout_method_continue', false);
        $this->waitForAjax();
        if ($this->isAlertPresent()) {
            $text = $this->getAlert();
            $this->assertEquals($text, 'Please choose to register or to checkout as a guest');
        }
        $this->checkoutOnePageHelper()->assertOnePageCheckoutTabOpened('checkout_method');
    }

    /**
     * <p>Empty required fields in billing address tab</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Leave billing information fields empty</p>
     * <p>4. Click "Continue" button</p>
     * <p>5. Verify error message;</p>
     * <p>6. Repeat scenario for all required fields in current tab;</p>
     * <p>Expected result:</p>
     * <p>Error message appears</p>
     *
     * @dataProvider emptyFieldsBilling
     * @depends preconditionsForTests
     * @test
     */
    public function frontEmptyRequiredFildsInBillingAddress($field, $fieldType, $simpleSku)
    {
        //Data
        $checkoutData = $this->loadData('with_register_empty_billing_fields',
                array('general_name' => $simpleSku, $field => ''));
        //Steps
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        //Verification
        $this->addFieldIdToMessage($fieldType, $field);
        $messagesCount = 1;
        if ($fieldType == 'dropdown') {
            $this->assertTrue($this->validationMessage('please_select_option'), $this->messages);
        } elseif ($field == 'billing_password') {
            $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
            $this->addFieldIdToMessage($fieldType, 'billing_confirm_password');
            $this->assertTrue($this->validationMessage('different_passwords'), $this->messages);
            $messagesCount = 2;
        } else {
            $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        }
        $this->assertTrue($this->verifyMessagesCount($messagesCount), $this->messages);
    }

    public function emptyFieldsBilling()
    {
        return array(
            array('billing_first_name', 'field'),
            array('billing_last_name', 'field'),
            array('billing_email', 'field'),
            array('billing_street_address_1', 'field'),
            array('billing_city', 'field'),
            array('billing_state', 'dropdown'),
            array('billing_zip_code', 'field'),
            array('billing_country', 'dropdown'),
            array('billing_telephone', 'field'),
            array('billing_password', 'field'),
            array('billing_confirm_password', 'field')
        );
    }

    /**
     * <p>Incorrect password length</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in required fields by regular data. </p>
     * <p>4. Fill in 'Password' field by values with incorrect length.</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Error message appears</p>
     *
     * @depends preconditionsForTests
     * @test
     */
    public function incorrectPasswordLength($simpleSku)
    {
        //Data
        $billingPassword = $this->generate('string', 5, ':punct:');
        $checkoutData = $this->loadData('with_register_empty_billing_fields',
                array('general_name' => $simpleSku, 'billing_password' => $billingPassword,
            'billing_confirm_password' => $billingPassword));
        //Steps
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        //Verification
        $this->addFieldIdToMessage('field', 'billing_password');
        $this->assertTrue($this->errorMessage('invalid_password_length'), $this->messages);
    }

    /**
     * <p>Incorrect Email</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in required fields by regular data. </p>
     * <p>4. Fill in 'Email' field by incorrect values.</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Error message appears</p>
     *
     * @depends preconditionsForTests
     * @dataProvider dataInvalidEmail
     * @test
     */
    public function incorrectEmail($wrongValue, $simpleSku)
    {
        //Data
        $checkoutData = $this->loadData('with_register_empty_billing_fields',
                array('general_name' => $simpleSku, 'billing_email' => $wrongValue));
        //Steps
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        //Verification
        $this->addFieldIdToMessage('field', 'billing_email');
        $this->assertTrue($this->errorMessage('invalid_email_address'), $this->messages);
    }

    public function dataInvalidEmail()
    {
        return array(
            array('invalid'),
            array('test@invalidDomain'),
            array('te@st@domain.com')
        );
    }

    /**
     * <p>Exist Email</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in required fields by regular data. </p>
     * <p>4. Fill in 'Email' field by incorrect values.</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Error message appears</p>
     *
     * @depends preconditionsForTests
     * @test
     */
    public function existEmail($simpleSku)
    {
        //Data
        $userData = $this->loadData('customer_account_register');
        $checkoutData = $this->loadData('with_register_empty_billing_fields',
                array('general_name' => $simpleSku, 'billing_email' => $userData['email']));
        //Steps
        $this->logoutCustomer();
        $this->navigate('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->logoutCustomer();
        $error = 'no alert error';
        try {
            $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $error = $e->getMessage();
            $error = trim(preg_replace('/Failed asserting that false is true./', '', $error));
        }
        //Verification
        $this->assertEquals($this->_getControlXpath('message', 'exist_email_alert'), $error);
    }

    /**
     * @depends preconditionsForTests
     * @dataProvider specialData
     * @test
     */
    public function specialValuesForAddressFields($dataName, $simpleSku)
    {
        //Data
        $checkoutData = $this->loadData($dataName, array('general_name' => $simpleSku));
        //Steps
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }

    public function specialData()
    {
        return array(
            array('with_register_flatrate_checkmoney_long_address'),
            array('with_register_flatrate_checkmoney_special_address')
        );
    }

    /**
     * <p>Empty required fields in shipping address tab</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill billing information fields by regular data</p>
     * <p>4. Click 'Continue' button.</p>
     * <p>5. Leave shipping information fields empty</p>
     * <p>6. Click "Continue" button</p>
     * <p>7. Verify error message;</p>
     * <p>8. Repeat scenario for all required fields in current tab;</p>
     * <p>Expected result:</p>
     * <p>Error message appears</p>
     *
     * @dataProvider emptyFieldsShipping
     * @depends preconditionsForTests
     * @test
     */
    public function frontEmptyRequiredFildsInShippingAddress($field, $fieldType, $simpleSku)
    {
        //Data
        $checkoutData = $this->loadData('with_register_empty_shipping_fields',
                array('general_name' => $simpleSku, $field => ''));
        //Steps
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        //Verification
        $this->addFieldIdToMessage($fieldType, $field);
        if ($fieldType == 'dropdown') {
            $this->assertTrue($this->validationMessage('please_select_option'), $this->messages);
        } else {
            $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        }
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function emptyFieldsShipping()
    {
        return array(
            array('shipping_first_name', 'field'),
            array('shipping_last_name', 'field'),
            array('shipping_street_address_1', 'field'),
            array('shipping_city', 'field'),
            array('shipping_state', 'dropdown'),
            array('shipping_zip_code', 'field'),
            array('shipping_country', 'dropdown'),
            array('shipping_telephone', 'field')
        );
    }

    /**
     * <p>Using long values for fill billing information form</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in Billing Information tab</p>
     * <p>4. Select "Ship to different address" option</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>6. Fill in Shipping Information tab.</p>
     * <p>7. Fill in one field by long value</p>
     * <p>8. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Error masseges appears</p>
     *
     * @depends preconditionsForTests
     * @dataProvider dataAddressLongValues
     * @test
     */
    public function frontBillingAddressLongValues($field, $simpleSku)
    {
        //Data
        $checkoutData = $this->loadData('with_register_empty_billing_fields',
                array('general_name' => $simpleSku, 'billing_' . $field => $this->generate('string', 256, ':alpha:')));
        //Steps and Verification
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $error = 'no alert error';
        try {
            $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $error = $e->getMessage();
            $error = trim(preg_replace('/Failed asserting that false is true./', '', $error));
        }
        //Verifications
        if (!preg_match('/street_address/', $field)) {
            $xpath = $this->_getControlXpath('field', 'billing_' . $field)
                    . self::$xpathFieldNameWithValidationMessage;
            $this->addParameter('fieldName', trim($this->getText($xpath), " *\t\n\r"));
        } else {
            $this->addParameter('fieldName', 'Street Address');
        }
        $this->assertEquals($this->_getControlXpath('message', 'long_value_alert'), $error);
    }

    /**
     * <p>Using long values for fill billing information form</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in Billing Information tab</p>
     * <p>4. Fill in one field by long value</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>Error masseges appears</p>
     *
     * @depends preconditionsForTests
     * @dataProvider dataAddressLongValues
     * @test
     */
    public function frontShippingAddressLongValues($field, $simpleSku)
    {
        //Data
        $checkoutData = $this->loadData('with_register_empty_shipping_fields',
                array('general_name' => $simpleSku, 'shipping_' . $field => $this->generate('string', 256, ':alpha:')));
        //Steps and Verification
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $error = 'no alert error';
        try {
            $this->checkoutOnePageHelper()->doOnePageCheckoutSteps($checkoutData);
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $error = $e->getMessage();
            $error = trim(preg_replace('/Failed asserting that false is true./', '', $error));
        }
        if (!preg_match('/street_address/', $field)) {
            $xpath = $this->_getControlXpath('field', 'shipping_' . $field)
                    . self::$xpathFieldNameWithValidationMessage;
            $this->addParameter('fieldName', trim($this->getText($xpath), " *\t\n\r"));
        } else {
            $this->addParameter('fieldName', 'Street Address');
        }
        $this->assertEquals($this->_getControlXpath('message', 'long_value_alert'), $error);
    }

    public function dataAddressLongValues()
    {
        return array(
            array('first_name'),
            array('last_name'),
            array('company'),
            array('street_address_1'),
            array('street_address_2'),
            array('city'),
            array('telephone'),
            array('fax')
        );
    }

}
