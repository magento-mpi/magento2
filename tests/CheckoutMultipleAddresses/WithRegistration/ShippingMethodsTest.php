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
 * Tests for shipping methods. Frontend
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CheckoutMultipleAddresses_WithRegistration_ShippingMethodsTest extends Mage_Selenium_TestCase
{
     /**
     * <p>Add Store Name for DHL tests</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('store_information');
    }

    /**
     */
    protected function assertPreConditions()
    {
        $this->logoutCustomer();
        $this->loginAdminUser();
    }

    /**
     * <p>Create a Simple Product for tests</p>
     *
     * @test
     */
    public function createSimpleProducts()
    {
        $this->navigate('manage_products');
        $productData1 = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData1);
        $this->assertMessagePresent('success', 'success_saved_product');
        $productData2 = $this->loadData('simple_product_for_order', null, array('general_name', 'general_sku'));
        $this->productHelper()->createProduct($productData2);
        $this->assertMessagePresent('success', 'success_saved_product');

        return array($productData1['general_name'], $productData2['general_name']);
    }

    /**
     * <p>Create a Virtual Product for tests</p>
     *
     * @test
     */
    public function createVirtualProduct()
    {
        $productData = $this->loadData('virtual_product_for_order', null, array('general_name', 'general_sku'));
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'virtual');
        $this->assertMessagePresent('success', 'success_saved_product');

        return $productData['general_name'];
    }

    /**
     * <p>Steps:</p>
     * <p>1. Configure settings in System->Configuration</p>
     * <p>2. Add 2 simple products to the shopping cart</p>
     * <p>3. Checkout with multiple addresses</p>
     * <p>4. Add default shipping address when needed. Add new shipping address</p>
     * <p>5. Set each product to be delivered to a separate address</p>
     * <p>6. Continue with default billing address, Check/Money payment method and appropriate shipping method</p>
     * <p>7. Place the order</p>
     * <p>Expected result:</p>
     * <p>Two new orders are successfully created.</p>
     * @TODO change to create shipping addresses once for all tests
     *
     * @dataProvider shipmentDataProvider
     * @depends createSimpleProducts
     *
     * @test
     */
    public function differentShippingMethods($shipment, $shippingOrigin, $simpleProductNames)
    {
        if(strpos($shipment,'dhl') !== false) {
            $this->markTestIncomplete('Temporary disabled DHL tests until a problem with DHL accounts is solved'
                                      . '\n Note that datasets and UImaps need to be updated as well then.');
        }
        //Data
        $shippingMethod = $this->loadData('multiple_front_shipping_' . $shipment);
        $checkoutData = $this->loadData('multiple_shipping_methods_register',
                array('shipping_method' => $shippingMethod));
        $checkoutData['products_to_add']['product_1']['general_name'] = $simpleProductNames[0];
        $checkoutData['products_to_add']['product_2']['general_name'] = $simpleProductNames[1];
        $checkoutData['shipping_address_data']['address_1']['general_name'] = $simpleProductNames[0];
        $checkoutData['shipping_address_data']['address_2']['general_name'] = $simpleProductNames[1];
        //Setup
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('shipping_settings_' . strtolower($shippingOrigin));
        $this->systemConfigurationHelper()->configure('shipping_disable');
        $this->systemConfigurationHelper()->configure($shipment . '_enable');
        //Steps and Verify
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData);
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    /**
     * <p>Steps:</p>
     * <p>1. Configure settings in System->Configuration</p>
     * <p>2. Add 1 simple product and 1 virtual to the shopping cart</p>
     * <p>3. Checkout with multiple addresses</p>
     * <p>4. Add default shipping address when needed. Add new shipping address</p>
     * <p>5. Set each product to be delivered to a separate address</p>
     * <p>6. Continue with default billing address, Check/Money payment method and appropriate shipping method</p>
     * <p>7. Place the order</p>
     * <p>Expected result:</p>
     * <p>Two new orders are successfully created.</p>
     *
     * @dataProvider shipmentDataProvider
     * @depends createSimpleProducts
     * @depends createVirtualProduct
     *
     * @test
     */
    public function differentShippingMethodsWithVirtualProduct($shipment, $shippingOrigin,
            $simpleProductNames, $virtualProductName)
    {
        if(strpos($shipment,'dhl') !== false) {
            $this->markTestIncomplete('Temporary disabled DHL tests until a problem with DHL accounts is solved'
                                      . '\n Note that datasets and UImaps need to be updated as well then.');
        }
        //Data
        $shippingMethod = $this->loadData('multiple_front_shipping_' . $shipment);
        $checkoutData = $this->loadData('multiple_shipping_methods_register',
                array('shipping_method' => $shippingMethod,
                            'address_2' => '%noValue%', 'address_to_add_2' => '%noValue%'));
        $checkoutData['products_to_add']['product_1']['general_name'] = $simpleProductNames[0];
        $checkoutData['products_to_add']['product_2']['general_name'] = $virtualProductName;
        $checkoutData['shipping_address_data']['address_1']['general_name'] = $simpleProductNames[0];
        //Setup
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('shipping_settings_' . strtolower($shippingOrigin));
        $this->systemConfigurationHelper()->configure('shipping_disable');
        $this->systemConfigurationHelper()->configure($shipment . '_enable');
        //Steps and Verify
        $orderNumbers = $this->checkoutMultipleAddressesHelper()->frontCreateMultipleCheckout($checkoutData);
        $this->assertTrue(count($orderNumbers) == 2, $this->getMessagesOnPage());
    }

    public function shipmentDataProvider()
    {
        return array(
            array('flatrate', 'usa'),
            array('free', 'usa'),
            array('ups', 'usa'),
            array('upsxml', 'usa'),
            array('usps', 'usa'),
            array('fedex', 'usa'),
            array('dhl_int', 'france'),
            array('dhl_usa', 'usa'),
        );
    }
}
