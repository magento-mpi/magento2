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
class CheckoutOnePage_WithRegistration_ShippingMethodsTest extends Mage_Selenium_TestCase
{

    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->addParameter('id', '');
    }

    /**
     * <p>Creating Simple product</p>
     *
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $simple = $this->loadData('simple_product_for_order');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);

        return $simple['general_name'];
    }

    /**
     * <p>Different Shipping Methods.</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created.</p>
     * <p>Steps:</p>
     * <p>1. Open product page.</p>
     * <p>2. Add product to Shopping Cart.</p>
     * <p>3. Click "Proceed to Checkout".</p>
     * <p>4. Select Checkout Method with Registering</p>
     * <p>4. Fill in Billing Information tab.</p>
     * <p>5. Select "Ship to this address" option.</p>
     * <p>6. Click 'Continue' button.</p>
     * <p>7. Select Shipping Method(by data provider).</p>
     * <p>8. Click 'Continue' button.</p>
     * <p>9. Select Payment Method.</p>
     * <p>10. Click 'Continue' button.</p>
     * <p>11. Verify information into "Order Review" tab</p>
     * <p>12. Place order.</p>
     * <p>Expected result:</p>
     * <p>Checkout is successful.</p>
     *
     * @depends preconditionsForTests
     * @dataProvider dataShipment
     * @test
     */
    public function differentShippingMethods($shipping, $simpleSku)
    {
        $checkoutData = $this->loadData('with_register_flatrate_checkmoney',
                array('general_name' => $simpleSku, 'shipping_data' => $this->loadData('front_shipping_' . $shipping)));
        //Steps
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($shipping . '_enable');
        $this->logoutCustomer();
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertTrue($this->successMessage('success_checkout'), $this->messages);
    }

    public function dataShipment()
    {
        return array(
            array('flatrate'),
            array('free'),
            array('ups'),
            array('upsxml'),
            array('usps'),
            array('fedex'),
//@TODO            array('dhl')
        );
    }

    protected function tearDown()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('shipping_disable');
    }

}
