<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CheckoutOnePage
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page Checkout  - checking validation tests
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CheckoutOnePage_WithRegistration_CheckingValidationTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->frontend();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
    }

    /**
     * <p>Creating Simple product</p>
     * @test
     * @return string
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps and Verification
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');

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
     * @TestlinkId TL-MAGE-5309
     */
    public function emptyShoppingCart()
    {
        //Steps
        $this->clickControl('link', 'checkout');
        $this->validatePage('shopping_cart');
        //Verifying
        $this->assertTrue($this->controlIsPresent('message', 'shopping_cart_is_empty'), 'Shopping cart is not empty');
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
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5310
     */
    public function checkoutMethodNotDefined($simpleSku)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'with_register_flatrate_checkmoney_different_address',
            array('general_name' => $simpleSku, 'checkout_as_customer' => '%noValue%'));
        $message = 'Please choose to register or to checkout as a guest';
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        //Steps
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
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
     * @param string $field
     * @param string $message
     * @param string $simpleSku
     *
     * @test
     * @dataProvider emptyRequiredFieldsInBillingAddressDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3596
     */
    public function emptyRequiredFieldsInBillingAddress($field, $message, $simpleSku)
    {
        //Data
        if ($field != 'billing_country') {
            $override = array('general_name' => $simpleSku, $field => '');
        } else {
            $override = array('general_name' => $simpleSku, $field => '', 'billing_state' => '%noValue%');
        }
        $checkout = $this->loadDataSet('OnePageCheckout', 'with_register_flatrate_checkmoney_different_address',
            $override);
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        //Steps
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkout);
    }

    public function emptyRequiredFieldsInBillingAddressDataProvider()
    {
        return array(
            array('billing_first_name', '"First Name": This is a required field.'),
            array('billing_last_name', '"Last Name": This is a required field.'),
            array('billing_email', '"Email Address": This is a required field.'),
            array('billing_street_address_1', '"Address": This is a required field.'),
            array('billing_city', '"City": This is a required field.'),
            array('billing_state', '"State/Province": Please select an option.'),
            array('billing_zip_code', '"Zip/Postal Code": This is a required field.'),
            array('billing_country', '"Country": Please select an option.'),
            array('billing_telephone', '"Telephone": This is a required field.'),
            array('billing_password', '"Password": This is a required field.'),
            array('billing_confirm_password', '"Confirm Password": This is a required field.')
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
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3599
     */
    public function incorrectPasswordLength($simpleSku)
    {
        //Data
        $billingPassword = $this->generate('string', 5, ':punct:');
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'with_register_flatrate_checkmoney_different_address',
            array(
                'general_name' => $simpleSku,
                'billing_password' => $billingPassword,
                'billing_confirm_password' => $billingPassword
            )
        );
        $message = '"Password": Please enter 6 or more characters. Leading or trailing spaces will be ignored.';
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        //Steps
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
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
     * @param string $wrongValue
     * @param string $simpleSku
     *
     * @test
     * @dataProvider incorrectEmailDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3598
     */
    public function incorrectEmail($wrongValue, $simpleSku)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'with_register_flatrate_checkmoney_different_address',
            array(
                'general_name' => $simpleSku,
                'billing_email' => $wrongValue
            )
        );
        $message = 'Please enter a valid email address. For example johndoe@domain.com.';
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        //Steps
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
    }

    public function incorrectEmailDataProvider()
    {
        return array(
            array('invalid'),
            array('test@invalidDomain'),
            array('te@st@unknown-domain.com')
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
     * @param string $simpleSku
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3594
     */
    public function existEmail($simpleSku)
    {
        //Data
        $message = $this->getUimapPage('frontend', 'onepage_checkout')->findMessage('exist_email_alert');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'with_register_flatrate_checkmoney_different_address',
            array(
                'general_name' => $simpleSku,
                'billing_email' => $userData['email']
            )
        );
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
    }

    /**
     * @param string $dataName
     * @param string $simpleSku
     *
     * @test
     * @dataProvider specialValuesForAddressFieldsDataProvider
     * @depends preconditionsForTests
     */
    public function specialValuesForAddressFields($dataName, $simpleSku)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', $dataName, array('general_name' => $simpleSku));
        //Steps
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    public function specialValuesForAddressFieldsDataProvider()
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
     * @param string $field
     * @param string $message
     * @param string $simpleSku
     *
     * @test
     * @dataProvider emptyRequiredFieldsInShippingAddressDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3597
     */
    public function emptyRequiredFieldsInShippingAddress($field, $message, $simpleSku)
    {
        if ($field == 'shipping_state') {
            $this->markTestIncomplete('MAGETWO-8745');
        }
        //Data
        if ($field != 'shipping_country') {
            $override = array('general_name' => $simpleSku, $field => '');
        } else {
            $override = array('general_name' => $simpleSku, $field => '', 'shipping_state' => '%noValue%');
        }
        $checkout = $this->loadDataSet('OnePageCheckout', 'with_register_flatrate_checkmoney_different_address',
            $override);
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkout);
    }

    public function emptyRequiredFieldsInShippingAddressDataProvider()
    {
        return array(
            array('shipping_first_name', '"First Name": This is a required field.'),
            array('shipping_last_name', '"Last Name": This is a required field.'),
            array('shipping_street_address_1', '"Address": This is a required field.'),
            array('shipping_city', '"City": This is a required field.'),
            array('shipping_state', '"State/Province": Please select an option.'),
            array('shipping_zip_code', '"Zip/Postal Code": This is a required field.'),
            array('shipping_country', '"Country": Please select an option.'),
            array('shipping_telephone', '"Telephone": This is a required field.')
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
     * <p>Error massages appears</p>
     *
     * @param string $field
     * @param string $fieldName
     * @param string $simpleSku
     *
     * @test
     * @dataProvider addressLongValuesDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3595
     */
    public function billingAddressLongValues($field, $fieldName, $simpleSku)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'with_register_flatrate_checkmoney_different_address',
            array(
                'general_name' => $simpleSku,
                'billing_' . $field => $this->generate('string', 256, ':alpha:')
            )
        );
        //Steps and Verification
        $this->addParameter('fieldName', $fieldName);
        $message = $this->getUimapPage('frontend', 'onepage_checkout')->findMessage('long_value_alert');
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
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
     * <p>Error massages appears</p>
     *
     * @param string $field
     * @param string $fieldName
     * @param string $simpleSku
     *
     * @test
     * @dataProvider addressLongValuesDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5311
     */
    public function shippingAddressLongValues($field, $fieldName, $simpleSku)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'with_register_flatrate_checkmoney_different_address',
            array(
                'general_name' => $simpleSku,
                'shipping_' . $field => $this->generate('string', 256, ':alpha:')
            )
        );
        //Steps and Verification
        $this->addParameter('fieldName', $fieldName);
        $message = $this->getUimapPage('frontend', 'onepage_checkout')->findMessage('long_value_alert');
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
    }

    public function addressLongValuesDataProvider()
    {
        return array(
            array('first_name', 'First Name'),
            array('last_name', 'Last Name'),
            array('company', 'Company'),
            array('street_address_1', 'Street Address'),
            array('street_address_2', 'Street Address'),
            array('city', 'City'),
            array('telephone', 'Telephone'),
            array('fax', 'Fax')
        );
    }
}
