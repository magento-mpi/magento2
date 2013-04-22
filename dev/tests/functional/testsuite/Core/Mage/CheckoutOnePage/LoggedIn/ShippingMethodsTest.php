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
 * Tests for shipping methods. Frontend - OnePageCheckout
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CheckoutOnePage_LoggedIn_ShippingMethodsTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        //Data
        $config = $this->loadDataSet('ShippingSettings', 'store_information');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
    }

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTestClass()
    {
        //Data
        $config = $this->loadDataSet('ShippingMethod', 'shipping_disable');
        $settings = $this->loadDataSet('ShippingSettings', 'shipping_settings_default');
        //Steps
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->systemConfigurationHelper()->configure($settings);
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');

        //Steps and Verification
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array('simple' => $simple['general_name'],
            'user'   => array('email' => $userData['email'], 'password' => $userData['password']));
    }

    /**
     * <p>Different Shipping Methods.</p>
     *
     * @param string $shipping
     * @param string $shippingOrigin
     * @param string $shippingDestination
     * @param array $testData
     *
     * @test
     * @dataProvider shipmentDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3202
     */
    public function differentShippingMethods($shipping, $shippingOrigin, $shippingDestination, $testData)
    {
        //Data
        $shippingMethod = $this->loadDataSet('ShippingMethod', $shipping . '_enable');
        $shippingData = $this->loadDataSet('Shipping', 'shipping_' . $shipping);
        $checkoutData = $this->loadDataSet(
            'OnePageCheckout',
            'signedin_flatrate_checkmoney_' . $shippingDestination,
            array(
                'general_name' => $testData['simple'],
                'shipping_data' => $shippingData
            )
        );
        //Steps
        $this->navigate('system_configuration');
        if ($shippingOrigin) {
            $config = $this->loadDataSet('ShippingSettings', 'shipping_settings_' . strtolower($shippingOrigin));
            $this->systemConfigurationHelper()->configure($config);
        }
        $this->systemConfigurationHelper()->configure($shippingMethod);
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
    }

    public function shipmentDataProvider()
    {
        return array(
            array('flatrate', null, 'usa'),
            array('free', null, 'usa'),
            array('ups', 'usa', 'usa'),
            array('upsxml', 'usa', 'usa'),
            array('usps', 'usa', 'usa'),
            array('fedex', 'usa', 'usa'),
            array('dhl', 'usa', 'france')
        );
    }
}
