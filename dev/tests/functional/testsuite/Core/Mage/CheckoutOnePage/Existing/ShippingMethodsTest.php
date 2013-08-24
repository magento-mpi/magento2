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
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CheckoutOnePage_Existing_ShippingMethodsTest extends Mage_Selenium_TestCase
{
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingSettings/store_information');
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

    protected function tearDownAfterTestClass()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('ShippingMethod/shipping_disable');
        $this->systemConfigurationHelper()->configure('ShippingSettings/shipping_settings_default');
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @return array
     *
     * @test
     */
    public function preconditionsForTests()
    {
        $userData = $this->loadDataSet('Customers', 'customer_account_register');
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->frontend('customer_login');
        $this->customerHelper()->registerCustomer($userData);
        $this->assertMessagePresent('success', 'success_registration');

        return array(
            'email' => $userData['email'],
            'simple' => $simple['general_name']
        );
    }

    /**
     * <p>Different Shipping Methods.</p>
     *
     * @param string $shipping
     * @param string $shippingOrigin
     * @param string $destination
     * @param string $testData
     *
     * @test
     * @dataProvider shipmentDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-3187
     */
    public function differentShippingMethods($shipping, $shippingOrigin, $destination, $testData)
    {
        //Data
        $shippingMethod = $this->loadDataSet('ShippingMethod', $shipping . '_enable');
        $shippingData = $this->loadDataSet('Shipping', 'shipping_' . $shipping);
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'exist_flatrate_checkmoney_' . $destination, array(
            'general_name' => $testData['simple'],
            'email_address' => $testData['email'],
            'shipping_data' => $shippingData
        ));
        //Steps
        $this->navigate('system_configuration');
        if ($shippingOrigin) {
            $config = $this->loadDataSet('ShippingSettings', 'shipping_settings_' . strtolower($shippingOrigin));
            $this->systemConfigurationHelper()->configure($config);
        }
        $this->systemConfigurationHelper()->configure($shippingMethod);
        $this->frontend();
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
