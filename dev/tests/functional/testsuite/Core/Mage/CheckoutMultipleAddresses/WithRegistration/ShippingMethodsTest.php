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
class Core_Mage_CheckoutMultipleAddresses_WithRegistration_ShippingMethodsTest extends Mage_Selenium_TestCase
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
        $this->logoutCustomer();
        $this->shoppingCartHelper()->frontClearShoppingCart();
    }

    /**
     * @return array
     *
     * @test
     */
    public function preconditionsForTests()
    {
        //Steps and Verification
        $simple1 = $this->productHelper()->createSimpleProduct();
        $simple2 = $this->productHelper()->createSimpleProduct();
        $virtual = $this->productHelper()->createVirtualProduct();
        return array('products1' => array('product_1' => $simple1['simple']['product_name'],
                                          'product_2' => $simple2['simple']['product_name']),
                     'products2' => array('product_1' => $simple1['simple']['product_name'],
                                          'product_2' => $virtual['virtual']['product_name']));
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
     * @TestlinkId TL-MAGE-5323
     */
    public function withSimpleProducts($shipment, $testData)
    {
        //Data
        $shippingMethod = $this->loadDataSet('Shipping', 'shipping_' . $shipment);
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register',
            array('shipping' => $shippingMethod), $testData['products1']);
        $shippingSettings = $this->loadDataSet('ShippingMethod', $shipment . '_enable');
        //Setup
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($shippingSettings);
        //Steps and Verify
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
     * @TestlinkId TL-MAGE-5324
     */
    public function withSimpleAndVirtualProducts($shipment, $testData)
    {
        $shippingMethod = $this->loadDataSet('Shipping', 'shipping_' . $shipment);
        $checkoutData = $this->loadDataSet('MultipleAddressesCheckout', 'multiple_with_register_virtual',
            array('shipping' => $shippingMethod), $testData['products2']);
        $shippingSettings = $this->loadDataSet('ShippingMethod', $shipment . '_enable');
        //Setup
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($shippingSettings);
        //Steps and Verify
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
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontMultipleCheckout($checkoutData);
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    public function productTypesProvider()
    {
        return array(
            array('products1', 'multiple_with_register_france'),
            array('products2', 'multiple_with_register_france_virtual')
        );
    }
}