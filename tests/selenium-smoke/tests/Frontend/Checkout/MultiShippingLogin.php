<?php
/**
 * Abstract test class for Admin/Product/AddSimpleProduct module
 *
 * @author Magento Inc.
 */

class Frontend_Checkout_MultiShippingLogin extends Test_Frontend_Checkout_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp()
    {
        parent::setUp();
    }

    /**
     * Tests checkout with Multiple Shipping Addaress with customer Login
     *
     */

    function testMultiShippingLoginCheckout()
    {
        // Test Dara
        $paramArray = array (
            "password" =>  Core::getEnvConfig('frontend/checkout/mulitiShippingRegister/password'),
            "email" => Core::getEnvConfig('frontend/checkout/mulitiShippingRegister/email'),
            "productUrl" => Core::getEnvConfig('frontend/checkout/mulitiShippingRegister/productUrl'),
            "qty" => Core::getEnvConfig('frontend/checkout/mulitiShippingRegister/qty'),
        );
        $this->multiShippingLoginCheckout($paramArray);
    }
}
