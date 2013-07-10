<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CheckoutMultipleAddresses
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for Checkout with Multiple Addresses. Frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CheckoutMultipleAddresses_WithRegistration_InputDataValidationTest extends Mage_Selenium_TestCase
{
    protected function tearDownAfterTest()
    {
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
    }

    /**
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $simple1 = $this->productHelper()->createSimpleProduct();
        $simple2 = $this->productHelper()->createSimpleProduct();
        return array('product_1' => $simple1['simple']['product_name'],
                     'product_2' => $simple2['simple']['product_name']);
    }

    /**
     * <p>Customer registration.  Use email that already exist.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5314
     */
    public function withEmailThatAlreadyExists(array $testData)
    {
        $message = 'There is already an account with this email address. '
            . 'If you are sure that it is your email address, click here to get your password and access your account.';
        //Data
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register',
                                           array('email'=> $userData['email']), $testData);
        //Steps
        $this->loginAdminUser();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
    }

    /**
     * <p>Customer registration. Fill in only required fields. Use max long values for fields.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5315
     */
    public function withLongValues($testData)
    {
        $this->markTestIncomplete('MAGETWO-8239');
        //Data
        $address = $this->loadDataSet('MultipleAddressesCheckout', 'register_data_long');
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register',
                                           array('general_customer_data' => $address),
                                           $testData);
        //Steps
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    /**
     * <p>Customer registration with empty required field.</p>
     *
     * @param string $field
     * @param string $fieldName
     * @param array $testData
     *
     * @test
     * @dataProvider withRequiredFieldsEmptyDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5316
     */
    public function withRequiredFieldsEmpty($field, $fieldName, $testData)
    {
        //Data
        $address = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register/general_customer_data',
                                      array($field => ''));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register',
                                           array('general_customer_data' => $address),
                                           $testData);
        //Steps
        if ($field == 'country' || $field == 'state') {
            $message = '"' . $fieldName . '": Please select an option.';
        } else {
            $message = '"' . $fieldName . '": This is a required field.';
        }
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
    }

    public function withRequiredFieldsEmptyDataProvider()
    {
        return array(
            array('first_name', 'First Name'),
            array('last_name', 'Last Name'),
            array('email', 'Email Address'),
            array('telephone', 'Telephone'),
            array('street_address_1', 'Street Address'),
            array('city', 'City'),
            array('state', 'State/Province'),
            array('zip_code', 'Zip/Postal Code'),
            array('country', 'Country'),
            array('password', 'Password'),
            array('password_confirmation', 'Confirm Password')
        );
    }

    /**
     * <p>Customer registration. Fill in all required fields by using special characters(except the field "email").</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5317
     */
    public function withSpecialCharacters($testData)
    {
        //Data
        $address = $this->loadDataSet('MultipleAddressesCheckout', 'register_data_special');
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register',
                                           array('general_customer_data' => $address),
                                           $testData);
        //Steps
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertMessagePresent('success', 'success_checkout');
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    /**
     * <p>Customer registration with invalid value for 'Email' field</p>
     *
     * @param string $invalidEmail
     * @param array $testData
     *
     * @test
     * @dataProvider withInvalidEmailDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5320
     */
    public function withInvalidEmail($invalidEmail, $testData)
    {
        //Data
        $address = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register/general_customer_data',
                                      array('email' => $invalidEmail));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register',
                                           array('general_customer_data' => $address),
                                           $testData);
        $message = '"Email Address": Please enter a valid email address (for example, johndoe@domain.com.).';
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        //Steps
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
    }

    public function withInvalidEmailDataProvider()
    {
        return array(
            array('invalid'),
            array('test@invalidDomain'),
            array('te@st@unknown-domain.com'),
            array('.test@unknown-domain.com'),
        );
    }

    /**
     * <p>Customer registration with invalid value for 'Password' fields</p>
     *
     * @param string $invalidPassword
     * @param string $errorMessage
     * @param array $testData
     *
     * @test
     * @dataProvider withInvalidPasswordDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5321
     */
    public function withInvalidPassword($invalidPassword, $errorMessage, $testData)
    {
        //Data
        $address = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register/general_customer_data',
                                      $invalidPassword);
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register',
                                           array('general_customer_data' => $address),
                                           $testData);
        //Steps
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $errorMessage);
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
    }

    public function withInvalidPasswordDataProvider()
    {
        return array(
            array(array('password'              => 12345,
                        'password_confirmation' => 12345),
                  '"Password": Please enter 6 or more characters. Leading or trailing spaces will be ignored.'),
            array(array('password'              => 1234567,
                        'password_confirmation' => 12345678),
                  '"Confirm Password": Please enter the same value again.'),
        );
    }
}
