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
class CheckoutMultipleAddresses_LoggedIn_ShippingMethodsTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Some description</p>
     */
    public function setUpBeforeTests()
    {
        //Login as admin
        //Configure shipping methods:
        //Flat Rate
        //Free shipping
        //UPS - UPS
        //UPS - UPS XML
        //USPS
        //Fedex
        //DHL USA
        //DHL International
        //
        //Register as a customer
        //Login as the customer
        //Create 2 shipping addresses
    //

    }

    protected function assertPreConditions()
    {
        //Clear shopping cart
    }

    /**
     * @test
     */
    public function createSimpleProduct()
    {
        return 'simple product name';
    }

    /**
     * @test
     */
    public function createVirtualProduct()
    {
        return 'virtual product name';
    }

    /**
     * @dataProvider dataShipment
     * @depends createSimpleProduct
     * @test
     */
    public function differentShippingMethods($shipping, $simpleProduct)
    {
        //Open the simple product
        //Add 2 items to shopping cart
        //Checkout with multiple addresses
        //Set products to different shipping addresses
        //Select shipping method
        //Select 'Check / Money order' payment method
            //Verify shipping addresses for both products
            //Verify shipping methods for both products
            //Verify products quantity
        //Place order
            //Verify that Order Success page is displayed
            //Verify that 2 order links are displayed
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

    /**
     * @dataProvider dataShipment
     * @depends createSimpleProduct
     * @depends createVirtualProduct
     * @test
     */
    public function differentShippingMethods($shipping, $simpleProduct, $virtualProduct)
    {
        //Open the simple product
        //Add to cart
        //Open the virtual product
        //Add to cart
        //Checkout with multiple addresses
            //Verify that shipping address is not available for virtual product
        //Set shipping address for simple product
        //Select shipping method
            //Verify that virtual product is under 'Other Items in Your Order' section
        //Select 'Check / Money order' payment method
            //Verify shipping addresses for both products
            //Verify shipping methods for both products
            //Verify products quantity
        //Place order
            //Verify that Order Success page is displayed
            //Verify that 2 order links are displayed
    }

    protected function tearDown()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('shipping_disable');
    }


}
