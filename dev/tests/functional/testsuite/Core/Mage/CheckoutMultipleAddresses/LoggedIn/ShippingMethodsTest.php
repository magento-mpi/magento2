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
 * Tests for shipping methods. Frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_CheckoutMultipleAddresses_LoggedIn_ShippingMethodsTest extends Mage_Selenium_TestCase
{
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
        $simple1 = $this->productHelper()->createSimpleProduct();
        $simple2 = $this->productHelper()->createSimpleProduct();
        $virtual = $this->productHelper()->createVirtualProduct();
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        $this->assertMessagePresent('success', 'success_saved_customer');
        return array('products1' => array('product_1' => $simple1['simple']['product_name'],
                                          'product_2' => $simple2['simple']['product_name']),
                     'products2' => array('product_1' => $simple1['simple']['product_name'],
                                          'product_2' => $virtual['virtual']['product_name']),
                     'user'      => array('email'    => $userData['email'],
                                          'password' => $userData['password']));
    }

    /**
     * <p>Steps:</p>
     *
     * @param string $shipment
     * @param array $testData
     *
     * @test
     * @dataProvider shipmentDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5282
     */
    public function withSimpleProducts($shipment, $testData)
    {
        //Data
        $shippingMethod = $this->loadDataSet('Shipping', 'shipping_' . $shipment);
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_signed_in',
            array('shipping' => $shippingMethod), $testData['products1']);
        $shippingSettings = $this->loadDataSet('ShippingMethod', $shipment . '_enable');
        //Setup
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($shippingSettings);
        //Steps and Verify
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    /**
     * <p>Configure settings in System->Configuration</p>
     *
     * @param string $shipment
     * @param array $testData
     *
     * @test
     * @dataProvider shipmentDataProvider
     * @depends preconditionsForTests
     * @TestlinkId TL-MAGE-5283
     */
    public function withSimpleAndVirtualProducts($shipment, $testData)
    {
        //Data
        $shippingMethod = $this->loadDataSet('Shipping', 'shipping_' . $shipment);
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_signed_in_virtual',
            array('shipping' => $shippingMethod), $testData['products2']);
        $shippingSettings = $this->loadDataSet('ShippingMethod', $shipment . '_enable');
        //Setup
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($shippingSettings);
        //Steps and Verify
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    public function shipmentDataProvider()
    {
        return array(
            array('flatrate'),
            array('free'),
            array('ups'),
            array('upsxml'),
            array('usps'),
            array('fedex')
        );
    }

    /**
     * @param array $testData
     * @param string $dataSet
     * @param string $productTypes
     *
     * @test
     * @dataProvider productTypesProvider
     * @depends preconditionsForTests
     */
    public function withDhlMethod($productTypes, $dataSet, $testData)
    {
        //Data
        $shippingMethod = $this->loadDataSet('Shipping', 'shipping_dhl');
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', $dataSet, array('shipping' => $shippingMethod),
            $testData[$productTypes]);
        $shippingSettings = $this->loadDataSet('ShippingMethod', 'dhl_enable');
        $shippingOrigin = $this->loadDataSet('ShippingSettings', 'shipping_settings_usa');
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($shippingSettings);
        $this->systemConfigurationHelper()->configure($shippingOrigin);
        //Steps and Verify
        $this->customerHelper()->frontLoginCustomer($testData['user']);
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    public function productTypesProvider()
    {
        return array(
            array('products1', 'multiple_with_signed_in_france'),
            array('products2', 'multiple_with_signed_in_france_virtual')
        );
    }
}