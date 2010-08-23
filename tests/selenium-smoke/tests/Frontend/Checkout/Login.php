<?php
/**
 * Abstract test class for Admin/Product/AddSimpleProduct module
 *
 * @author Magento Inc.
 */

class Frontend_Checkout_Login extends Test_Frontend_Checkout_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp() {
        parent::setUp();

        // Get test parameters
    }

    /**
     * Tests checkout with login from FrontEnd
     *
     */

    function testLoginCheckout() {
        // Test Dara
        $paramArray = array (
            "password" =>  Core::getEnvConfig('frontend/checkout/login/password'),
            "email" => Core::getEnvConfig('frontend/checkout/login/email'),
            "productUrl" => Core::getEnvConfig('frontend/checkout/productUrl'),
            "qty" => Core::getEnvConfig('frontend/checkout/qty'),
        );
        $this->loginCheckout($paramArray);
    }
}
