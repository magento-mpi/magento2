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
 * One page Checkout tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CheckoutOnePage_LoggedIn_CheckingValidationTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('ShippingMethod', 'free_enable'));
        $this->systemConfigurationHelper()->configure($this->loadDataSet('PaymentMethod', 'savedcc_without_3Dsecure'));
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->frontend();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->logoutCustomer();
    }

    /**
     * <p>Creating Simple product and customer</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        //Steps and Verification
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/flatrate_enable');
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');

        return array(
            'sku' => $simple['general_name'],
            'customer' => array(
                'email' => $userData['email'],
                'password' => $userData['password']
            )
        );
    }

    /**
     * <p>Empty required fields in billing address tab</p>
     *
     * @param string $field
     * @param string $message
     * @param array $data
     *
     * @test
     * @dataProvider addressEmptyFieldsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3195
     */
    public function emptyRequiredFieldsInBillingAddress($field, $message, $data)
    {
        //Data
        $override = array('general_name' => $data['sku'], 'billing_' . $field => '');
        if ($field == 'country') {
            $override['billing_state'] = '%noValue%';
        }
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_different_address',
            $override);
        //Steps
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
    }

    /**
     * <p>Empty required fields in shipping address tab</p>
     *
     * @param string $field
     * @param string $message
     * @param array $data
     *
     * @test
     * @dataProvider addressEmptyFieldsDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3196
     */
    public function emptyRequiredFieldsInShippingAddress($field, $message, $data)
    {
        if ($field == 'state') {
            $this->markTestIncomplete('MAGETWO-8745');
        }
        //Data
        $override = array('general_name' => $data['sku'], 'shipping_' . $field => '');
        if ($field == 'country') {
            $override['shipping_state'] = '%noValue%';
        }
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_different_address',
            $override);
        //Steps
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
    }

    public function addressEmptyFieldsDataProvider()
    {
        return array(
            array('first_name', '"First Name": This is a required field.'),
            array('last_name', '"Last Name": This is a required field.'),
            array('street_address_1', '"Address": This is a required field.'),
            array('city', '"City": This is a required field.'),
            array('state', 'State/Province": Please select an option.'),
            array('zip_code', '"Zip/Postal Code": This is a required field.'),
            array('country', '"Country": Please select an option.'),
            array('telephone', '"Telephone": This is a required field.')
        );
    }

    /**
     * <p>Shipping method not defined</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3199
     */
    public function shippingMethodNotDefined($data)
    {
        //Data
        $message = $this->getUimapPage('frontend', 'onepage_checkout')->findMessage('shipping_alert');
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_different_address', array(
            'general_name' => $data['sku'],
            'shipping_data' => '%noValue%'
        ));
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
    }

    /**
     * <p>Payment method not defined</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3197
     */
    public function frontPaymentMethodNotDefined($data)
    {
        //Data
        $message = $this->getUimapPage('frontend', 'onepage_checkout')->findMessage('payment_alert');
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_different_address', array(
            'general_name' => $data['sku'],
            'payment_data' => '%noValue%'
        ));
        //Steps
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->setExpectedException('PHPUnit_Framework_AssertionFailedError', $message);
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
    }

    /**
     * <p>Verifying "Use Billing Address" checkbox functionality</p>
     *
     * @param array $data
     *
     * @test
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3198
     */
    public function frontShippingAddressUseBillingAddress($data)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'signedin_flatrate_checkmoney_use_billing_in_shipping',
            array('general_name' => $data['sku']));
        //Steps
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    /**
     * @param string $dataName
     * @param array $data
     *
     * @test
     * @dataProvider specialDataDataProvider
     * @depends preconditionsForTests
     */
    public function specialValuesForAddressFields($dataName, $data)
    {
        //Data
        $checkoutData = $this->loadDataSet('OnePageCheckout', $dataName, array('general_name' => $data['sku']));
        //Steps
        $this->customerHelper()->frontLoginCustomer($data['customer']);
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    public function specialDataDataProvider()
    {
        return array(
            array('signedin_flatrate_checkmoney_long_address'),
            array('signedin_flatrate_checkmoney_special_address')
        );
    }
}