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
        $shippingSettings = $this->loadDataSet('ShippingMethod', 'free_enable');
        $paymentSettings = $this->loadDataSet('PaymentMethod', 'savedcc_without_3Dsecure');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($shippingSettings);
        $this->systemConfigurationHelper()->configure($paymentSettings);
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
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        //Steps and Verification
        $this->loginAdminUser();
        $simple1 = $this->productHelper()->createSimpleProduct();
        $simple2 = $this->productHelper()->createSimpleProduct();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array('products' => array('product_1' => $simple1['simple']['product_name'],
                                         'product_2' => $simple2['simple']['product_name']),
                     'user'     => array('email'    => $userData['email'],
                                         'password' => $userData['password']));
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
                                                                                         $path,
                                                                                         array($emptyField => ''));
        //Steps
        if ($emptyField == 'state' || $emptyField == 'country') {
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
                                                                              $path,
                                                                              array($emptyField => ''));
        //Steps
        if ($emptyField == 'state' || $emptyField == 'country') {
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
     * @param array $testData
     *
     * @test
     * @dataProvider selectAddressesPageInvalidQtyDataProvider
     * @depends preconditionsForTests
     * @testlinkId TL-MAGE-5273
     */
    public function selectInvalidProductQty($invalidQty, $testData)
    {
        //Data
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_signed_in',
                                           array('product_2'     => '%noValue%',
                                                'address_data_2' => '%noValue%',
                                                'product_qty'    => $invalidQty,),
                                           array('product_1' => $testData['products']['product_1']));
        //Steps
        $this->frontend();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError',
                                    '"shopping_cart_is_empty" message(s) is on the page.');
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
    }

    public function selectAddressesPageInvalidQtyDataProvider()
    {
        return array(
            array('-10'),
            array($this->generate('string', 3, ':alpha:'))
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
                                           array('shipping'=> '%noValue%'),
                                           $testData['products']);
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
                                           array('payment'=> '%noValue%'),
                                           $testData['products']);
        //Steps
        $this->frontend();
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError',
                                    "Payment method is not defined");
        $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
    }

    /**
     * <p>Empty Card Info field </p>
     *
     * @param string $emptyField
     * @param string $fieldName
     * @param array $testData
     *
     * @test
     * @dataProvider emptyCardInfoDataProvider
     * @depends preconditionsForTests
     * @testlinkId TL-MAGE-5279
     */
    public function emptyCardInfo($emptyField, $fieldName, $testData)
    {
        //@TODO
        $messages = array('This is a required field.',
                          'Credit card number does not match credit card type.',
                          'Please enter a valid credit card verification number.',
                          'Card type does not match credit card number.');
        if ($fieldName) {
            $message = '"' . $fieldName . '": ' . $messages[0];
        } else {
            $message = $messages[0];
        }
        if ($emptyField == 'card_type') {
            $message .= "\n" . '"Credit Card Number": ' . $messages[1] . "\n" . '"ccsave_cc_cid": ' . $messages[2];
        }
        if ($emptyField == 'card_number') {
            $message = '"Credit Card Type": ' . $messages[3] . "\n\"" . $fieldName . '": ' . $messages[1];
        }

        $paymentData = $this->loadDataSet('Payment', 'payment_savedcc', array($emptyField => ''));
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_signed_in',
                                           array('payment'=> $paymentData),
                                           $testData['products']);
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
            array('name_on_card', 'Name on Card'),
            array('card_type', 'Credit Card Type'),
            array('card_number', 'Credit Card Number'),
            array('expiration_month', 'ccsave_expiration'),
            array('expiration_year', ''),
            array('card_verification_number', 'ccsave_cc_cid')
        );
    }
}