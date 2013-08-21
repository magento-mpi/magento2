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
class Core_Mage_CheckoutMultipleAddresses_LoggedIn_InputDataValidationTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->systemConfigurationHelper()->configure('ShippingMethod/free_enable');
        $this->systemConfigurationHelper()->configure('PaymentMethod/savedcc_without_3Dsecure');
    }

    protected function tearDownAfterTest()
    {
        $this->frontend();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
    }

    /**
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps and Verification
        $this->loginAdminUser();
        $simple1 = $this->productHelper()->createSimpleProduct();
        $simple2 = $this->productHelper()->createSimpleProduct();
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');

        return array(
            'products' => array(
                'product_1' => $simple1['simple']['product_name'],
                'product_2' => $simple2['simple']['product_name']
            ),
            'user' => array(
                'email' => $userData['email'],
                'password' => $userData['password']
            )
        );
    }

    /**
     * <p>Empty required fields(Select Addresses page)</p>
     *
     * @param string $emptyField
     * @param string $fieldName
     * @param array $testData
     *
     * @test
     * @dataProvider emptyRequiredFieldsDataProvider
     * @depends preconditionsForTests
     * @testlinkId TL-MAGE-5269
     */
    public function emptyRequiredFieldsInShippingAddress($emptyField, $fieldName, $testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_signed_in', null,
            $testData['products']);
        $path = 'multiple_with_signed_in/shipping_data/address_data_1/address';
        $checkoutData['shipping_data']['address_data_1']['address'] = $this->loadDataSet('MultipleAddressesCheckout',
            $path, array($emptyField => ''));
        //Steps
        if ($emptyField == 'country' || $emptyField == 'state') {
            $message = '"' . $fieldName . '": Please select an option.';
        } else {
            $message = '"' . $fieldName . '": This is a required field.';
        }
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        $this->frontend();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
    }

    /**
     * <p>Empty required fields(Select Addresses page)</p>
     *
     * @param string $emptyField
     * @param string $fieldName
     * @param array $testData
     *
     * @test
     * @dataProvider emptyRequiredFieldsDataProvider
     * @depends preconditionsForTests
     * @testlinkId TL-MAGE-5275
     */
    public function emptyRequiredFieldsInBillingAddress($emptyField, $fieldName, $testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_signed_in', null,
            $testData['products']);
        $path = 'multiple_with_signed_in/payment_data/billing_address';
        $checkoutData['payment_data']['billing_address'] = $this->loadDataSet('MultipleAddressesCheckout',
            $path, array($emptyField => ''));
        //Steps
        if ($emptyField == 'country' || $emptyField == 'state') {
            $message = '"' . $fieldName . '": Please select an option.';
        } else {
            $message = '"' . $fieldName . '": This is a required field.';
        }
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        $this->frontend();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
    }

    public function emptyRequiredFieldsDataProvider()
    {
        return array(
            array('first_name', 'First Name'),
            array('last_name', 'Last Name'),
            array('telephone', 'Telephone'),
            array('street_address_1', 'Street Address'),
            array('city', 'City'),
            array('state', 'State/Province'),
            array('zip_code', 'Zip/Postal Code'),
            array('country', 'Country')
        );
    }

    /**
     * <p>Fill in all required fields by using special characters</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @testlinkId TL-MAGE-5271
     */
    public function withSpecialCharsInShippingAddress($testData)
    {
        //Data
        $address = $this->loadDataSet('MultipleAddressesCheckout', 'special_symbols');
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_signed_in', null,
            $testData['products']);
        $checkoutData['shipping_data']['address_data_1']['address'] = $address;
        //Steps
        $this->frontend();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    /**
     * <p>Fill in only required fields. Use max long values for fields.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @testlinkId TL-MAGE-5272
     */
    public function withLongValuesInShippingAddress($testData)
    {
        $this->markTestIncomplete('MAGETWO-8239');
        //Data
        $address = $this->loadDataSet('MultipleAddressesCheckout', 'long_values');
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_signed_in', null,
            $testData['products']);
        $checkoutData['shipping_data']['address_data_1']['address'] = $address;
        //Steps
        $this->frontend();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    /**
     * <p>Fill in all required fields by using special characters</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @testlinkId TL-MAGE-5276
     */
    public function withSpecialCharsInBillingAddress($testData)
    {
        //Data
        $address = $this->loadDataSet('MultipleAddressesCheckout', 'special_symbols');
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_signed_in', null,
            $testData['products']);
        $checkoutData['payment_data']['billing_address'] = $address;
        //Steps
        $this->frontend();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    /**
     * <p>Fill in only required fields. Use max long values for fields.</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @testlinkId TL-MAGE-5277
     */
    public function withLongValuesInBillingAddress($testData)
    {
        $this->markTestIncomplete('MAGETWO-8239');
        //Data
        $address = $this->loadDataSet('MultipleAddressesCheckout', 'long_values');
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_signed_in', null,
            $testData['products']);
        $checkoutData['payment_data']['billing_address'] = $address;
        //Steps
        $this->frontend();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    /**
     * <p>Fill in only required fields. Use max long values for fields.</p>
     *
     * @param string $invalidQty
     * @param string $message
     * @param array $testData
     *
     * @test
     * @dataProvider selectAddressesPageInvalidQtyDataProvider
     * @depends preconditionsForTests
     * @testlinkId TL-MAGE-5273
     */
    public function selectInvalidProductQty($invalidQty, $message, $testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_signed_in',
            array('product_2' => '%noValue%', 'address_data_2' => '%noValue%', 'product_qty' => $invalidQty),
            array('product_1' => $testData['products']['product_1']));
        //Steps
        $this->frontend();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
    }

    public function selectAddressesPageInvalidQtyDataProvider()
    {
        return array(
            array('-10', '"shopping_cart_is_empty" message(s) is on the page.'),
            array($this->generate('string', 3, ':alpha:'), 'Please enter a valid number.')
        );
    }

    /**
     * <p>Shipping Method is not selected</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @testlinkId TL-MAGE-5274
     */
    public function shippingMethodNotSelected($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_signed_in',
            array('shipping' => '%noValue%'), $testData['products']);
        //Steps
        $this->frontend();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError',
            "Please select shipping methods for all addresses");
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
    }

    /**
     * <p>Payment Method is not selected</p>
     *
     * @param array $testData
     *
     * @test
     * @depends preconditionsForTests
     * @testlinkId TL-MAGE-5278
     */
    public function paymentMethodNotSelected($testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_signed_in',
            array('payment' => '%noValue%'), $testData['products']);
        //Steps
        $this->frontend();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', "Please specify payment method.");
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
    }

    /**
     * <p>Empty Card Info field </p>
     *
     * @param string $emptyField
     * @param string $message
     * @param array $testData
     *
     * @test
     * @dataProvider emptyCardInfoDataProvider
     * @depends preconditionsForTests
     * @testlinkId TL-MAGE-5279
     */
    public function emptyCardInfo($emptyField, $message, $testData)
    {
        if ($emptyField == 'card_type') {
            $message .= "\n" . '"Credit Card Number": Credit card number does not match credit card type.'
                . "\n" . '"Card Verification Number": Please enter a valid credit card verification number.';
        }
        if ($emptyField == 'card_number') {
            $message = '"Credit Card Type": Card type does not match credit card number.' . "\n" . $message;
        }
        $paymentData = $this->loadDataSet('Payment', 'payment_savedcc', array($emptyField => ''));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_signed_in',
            array('payment' => $paymentData), $testData['products']);
        //Steps
        $this->frontend();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
    }

    public function emptyCardInfoDataProvider()
    {
        return array(
            array('name_on_card', '"Name on Card": This is a required field.'),
            array('card_type', '"Credit Card Type": This is a required field.'),
            array('card_number', '"Credit Card Number": This is a required field.'),
            array('expiration_month', '"Expiration Date": This is a required field.'),
            array('expiration_year', '"ccsave_expiration_yr": This is a required field.'),
            array('card_verification_number', '"Card Verification Number": This is a required field.')
        );
    }
}
