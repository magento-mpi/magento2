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

    /**
     *
     * <p>Creating products for testing.</p>
     *
     * <p>Navigate to Sales-Orders page.</p>
     *
     */
    protected function assertPreConditions()
    {
        $this->frontend();
        $this->assertTrue($this->checkCurrentPage('home'), $this->messages);
        $this->addParameter('id', '0');
    }

    /**
     * <p>Creating Simple product with required fields only</p>
     * <p>Steps:</p>
     * <p>1. Click "Add product" button;</p>
     * <p>2. Fill in "Attribute Set" and "Product Type" fields;</p>
     * <p>3. Click "Continue" button;</p>
     * <p>4. Fill in required fields;</p>
     * <p>5. Click "Save" button;</p>
     * <p>Expected result:</p>
     * <p>Product is created, confirmation message appears;</p>
     *
     * @test
     */
    public function createSimple()
    {
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);
        //Data
        $productData = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        //Steps
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_products'), $this->messages);

        return $productData['general_name'];
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
        $this->clickControl('link', 'checkout');
        //Verifying
        $xPath = $this->_getControlXpath('message', 'shopping_cart_is_empty');
        $this->assertTrue($this->isElementPresent($xPath));
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
     * @depends createSimple
     * @test
     */
    public function frontCheckoutMethodNotDefined($productData)
    {
        //Data
        $checkoutData = $this->loadData('checkout_data_undefined', array('general_name' => $productData));
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData, FALSE);
        //Verification
        $text = 'Please choose to register or to checkout as a guest.';
        $alert = (!$this->isAlertPresent($text)) ? FALSE : TRUE;
        if ($alert == TRUE) {
            $this->getAlert();
            $this->assertTrue($alert);
        } else {
            $this->fail('Alert is not appeared');
        }
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
     * @depends createSimple
     * @test
     */
    public function frontEmptyRequiredFildsInBillingAddress($emptyFieldBilling, $productData)
    {
        //Data
        $checkoutData = $this->loadData('checkout_data_billing_empty_fields',
                array('general_name' => $productData, $emptyFieldBilling => ''));
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $page = $this->getUimapPage('frontend', 'onepage_checkout');
        $fieldSet = $page->findFieldset('billing_information');
        if ($emptyFieldBilling != 'billing_country' && $emptyFieldBilling != 'billing_state' &&
                $emptyFieldBilling != 'billing_password') {
            $fieldXpath = $fieldSet->findField($emptyFieldBilling);
            $this->addParameter('fieldXpath', $fieldXpath);
            $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
            $this->assertTrue($this->verifyMessagesCount(), $this->messages);
        } elseif ($emptyFieldBilling == 'billing_password') {
            $fieldXpath = $fieldSet->findField($emptyFieldBilling);
            $this->addParameter('fieldXpath', $fieldXpath);
            $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
            $fieldXpath = $fieldSet->findField('billing_confirm_password');
            $this->addParameter('fieldXpath', $fieldXpath);
            $this->assertTrue($this->errorMessage('different_passwords'), $this->messages);
            $this->assertTrue($this->verifyMessagesCount(2), $this->messages);
        } else {
            $fieldXpath = $fieldSet->findDropdown($emptyFieldBilling);
            $this->addParameter('fieldXpath', $fieldXpath);
            $this->assertTrue($this->errorMessage('please_select_option'), $this->messages);
            $this->assertTrue($this->verifyMessagesCount(), $this->messages);
        }
    }

    public function emptyFieldsBilling()
    {
        return array(
            array('billing_first_name'),
            array('billing_last_name'),
            array('billing_email'),
            array('billing_street_address_1'),
            array('billing_city'),
            array('billing_state'),
            array('billing_zip_code'),
            array('billing_country'),
            array('billing_telephone'),
            array('billing_password'),
            array('billing_confirm_password')
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
     * @depends createSimple
     * @test
     */
    public function frontBillingAddressInvalidEmail($productData)
    {
        //Data
        $billingPassword = $this->generate('string', 5, ':punct:');


        $checkoutData = $this->loadData('checkout_data_billing_empty_fields',
                array('general_name' => $productData, 'billing_password' => $billingPassword,
                      'billing_confirm_password' => $billingPassword), array('billing_email'));
        print_r($checkoutData);
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertTrue($this->errorMessage('invalid_password_length'), $this->messages);
    }

    /**
     * <p>Filling required fields by invalid values(Except Email)</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in 'Email' field by regular data.</p>
     * <p>4. Fill other required fields by incorrect data.</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Error message appears</p>
     *
     * @depends createSimple
     * @dataProvider billingInvalidValues
     * @test
     */
    public function frontBillingAddressInvalidValuesRequiredFields($invalidValues, $productData)
    {
        //Data
        $checkoutData = $this->loadData('checkout_data_register',
                array('general_name' => $productData, $invalidValues => $this->generate('string', 32, ':punct:')),
                array('billing_email'));
        print_r($checkoutData);
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }

    public function billingInvalidValues()
    {
        return array(
            array('billing_first_name'),
            array('billing_last_name'),
            array('billing_street_address_1'),
            array('billing_city'),
            array('billing_zip_code'),
            array('billing_telephone'));
    }

    /**
     * <p>Incorrect Email format</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in 'Email' field by incorrect value.</p>
     * <p>4. Fill other required fields by regular data.</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Error message appears</p>
     *
     * @depends createSimple
     * @test
     */
    public function frontBillingAddressIncorrectEmailFormat($productData)
    {
        //Data
        $checkoutData = $this->loadData('checkout_data_billing_empty_fields',
                array('general_name' => $productData,
            'billing_email' => $this->generate('string', 32, ':alnum:')));
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $page = $this->getUimapPage('frontend', 'onepage_checkout');
        $fieldSet = $page->findFieldset('billing_information');
        $fieldXpath = $fieldSet->findField('billing_email');
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->errorMessage('invalid_email_address'), $this->messages);
    }

    /**
     * <p>Using existing Email Address for fill billing information form</p>
     * <p>Preconditions</p>
     * <p>1. Create customer</p>
     * <p>2. Add product to Shopping Cart</p>
     * <p>3. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in 'Email' field by existing email.</p>
     * <p>4. Fill other required fields by regular data.</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Error message appears</p>
     *
     * @depends createSimple
     * @test
     */
    public function frontBillingAddressExistingEmail($productData)
    {
        //Preconditions
        $userData = $this->loadData('generic_customer_account', null, 'email');
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->assertTrue($this->checkCurrentPage('manage_customers'), $this->messages);
        $this->CustomerHelper()->createCustomer($userData);
        $this->assertTrue($this->successMessage('success_saved_customer'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_customers'), $this->messages);
        //Data
        $checkoutData = $this->loadData('checkout_data_billing_empty_fields',
                array('general_name' => $productData, 'billing_email' => $userData['email']));
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData, FALSE);
        //Verification
        $this->waitForAjax();
        $text = 'There is already a customer registered using this email address.
            Please login using this email address or enter a different email address to register your account.';
        $alert = (!$this->isAlertPresent($text)) ? FALSE : TRUE;
        if ($alert == TRUE) {
            $this->getAlert();
            $this->assertTrue($alert);
        } else {
            $this->fail('Alert' . $text . 'has not appeared');
        }
    }

    /**
     * <p>Using incorrect confirmation password</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill required fields by regular data.</p>
     * <p>4. Enter incorrect confirmation password</p>
     * <p>4. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Error message appears - "Please make sure your passwords match."</p>
     *
     * @depends createSimple
     * @test
     */
    public function frontBillingIncorrectConfirmationPassword($productData)
    {
        //Data
        $checkoutData = $this->loadData('checkout_data_billing_empty_fields',
                array('general_name' => $productData,
            'billing_confirm_password' => $this->generate('string', 32, ':punct:')), array('billing_email'));
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $page = $this->getUimapPage('frontend', 'onepage_checkout');
        $fieldSet = $page->findFieldset('billing_information');
        $fieldXpath = $fieldSet->findField('billing_confirm_password');
        $this->addParameter('fieldXpath', $fieldXpath);
        $this->assertTrue($this->errorMessage('different_passwords'), $this->messages);
        //Postconditions
        $this->logoutCustomer();
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
     * @depends createSimple
     * @test
     */
    public function frontEmptyRequiredFildsInShippingAddress($emptyFieldShipping, $productData)
    {
        //Data
        $checkoutData = $this->loadData('checkout_data_shipping_empty_fields',
                array('general_name' => $productData, $emptyFieldShipping => ''));
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $page = $this->getUimapPage('frontend', 'onepage_checkout');
        $fieldSet = $page->findFieldset('shipping_information');
        if ($emptyFieldShipping != 'shipping_country' && $emptyFieldShipping != 'shipping_state') {
            $fieldXpath = $fieldSet->findField($emptyFieldShipping);
            $this->addParameter('fieldXpath', $fieldXpath);
            $this->assertTrue($this->errorMessage('empty_required_field'), $this->messages);
            $this->assertTrue($this->verifyMessagesCount(), $this->messages);
        } else {
            $fieldXpath = $fieldSet->findDropdown($emptyFieldShipping);
            $this->addParameter('fieldXpath', $fieldXpath);
            $this->assertTrue($this->errorMessage('please_select_option'), $this->messages);
            $this->assertTrue($this->verifyMessagesCount(), $this->messages);
        }
    }

    public function emptyFieldsShipping()
    {
        return array(
            array('shipping_first_name'),
            array('shipping_last_name'),
            array('shipping_street_address_1'),
            array('shipping_city'),
            array('shipping_state'),
            array('shipping_zip_code'),
            array('shipping_country'),
            array('shipping_telephone')
        );
    }

    /**
     * <p>Using long values for fill shipping information form</p>
     * <p>Preconditions</p>
     * <p>1. Add product to Shopping Cart</p>
     * <p>2. Click "Proceed to Checkout"</p>
     * <p>Steps</p>
     * <p>1. Fill in Checkout Method tab</p>
     * <p>2. Click 'Continue' button.</p>
     * <p>3. Fill in Billing Information tab</p>
     * <p>4. Select "Ship to different address" option</p>
     * <p>5. Click 'Continue' button.</p>
     * <p>6. Fill in Shipping Information required fields by special characters.</p>
     * <p>7. Click 'Continue' button.</p>
     * <p>Expected result:</p>
     * <p>Customer successfully redirected to the next page, no error masseges appears</p>
     *
     * @depends createSimple
     * @test
     */
    public function frontShippingAddressLongValues($productData)
    {

        //Data
        $checkoutData = $this->loadData('checkout_data_shipping_empty_fields',
                array('general_name' => $productData,
            'shipping_first_name' => $this->generate('string', 256, ':punct:'),
            'shipping_last_name' => $this->generate('string', 256, ':punct:'),
            'shipping_street_address_1' => $this->generate('string', 256, ':punct:'),
            'shipping_city' => $this->generate('string', 256, ':punct:'),
            'shipping_zip_code' => $this->generate('string', 256, ':punct:'),
            'shipping_telephone' => $this->generate('string', 256, ':punct:')), array('billing_email'));
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData, FALSE);
        //Verification
        $text = $this->_getControlXpath('message', 'billing_long_values_data_alert');
        $this->waitForAjax();
        $alert = (!$this->isAlertPresent($text)) ? FALSE : TRUE;
        if ($alert == TRUE) {
            $this->getAlert();
            $this->assertTrue($alert);
        } else {
            $this->fail('Alert is not appeared');
        }
        //Postconditions
        $this->logoutCustomer();
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
     * @depends createSimple
     * @test
     */
    public function frontShippingMethodNotDefined($productData)
    {
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->systemConfigurationHelper()->configure('free_enable');
        $this->assertTrue($this->successMessage('success_saved_config'), $this->messages);
        //Data
        $checkoutData = $this->loadData('checkout_data_shipping_method_undefined',
                                        array('general_name' => $productData));
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData, FALSE);
        //Verification
        $text = 'Please specify shipping method.';
        $alert = (!$this->isAlertPresent($text)) ? FALSE : TRUE;
        if ($alert == TRUE) {
            $this->getAlert();
            $this->assertTrue($alert);
        } else {
            $this->fail('Alert has not appeared');
        }
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
     * @depends createSimple
     * @test
     */
    public function frontPaymentMethodNotDefined($productData)
    {
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->assertTrue($this->checkCurrentPage('system_configuration'), $this->messages);
        $this->systemConfigurationHelper()->configure('savedcc_without_3Dsecure');
        $this->assertTrue($this->successMessage('success_saved_config'), $this->messages);
        //Data
        $checkoutData = $this->loadData('checkout_data_payment_method_undefined',
                                        array('general_name' => $productData));
        //Steps
        $this->assertTrue($this->logoutCustomer());
        $this->assertTrue($this->frontend('home'));
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData, FALSE);
        //Verification
        $setXpath = $this->_getControlXpath('fieldset', 'payment_method') . "[contains(@class,'active')]";
        $this->waitForElement($setXpath);
        if ($this->isElementPresent($setXpath)) {
            $this->clickButton('payment_method_continue', FALSE);
        } else {
            $this->fail('Seems here is only one payment method and we have nothing to choose');
        }
        //Verification
        $text = $this->_getControlXpath('message', 'payment_alert');
        $alert = (!$this->isAlertPresent($text)) ? FALSE : TRUE;
        if ($alert == TRUE) {
            $this->getAlert();
        } else {
            $this->fail('Alert ' . $text . ' has not appeared.');
        }
    }
}

?>
