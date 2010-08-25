<?php
/**
 * Abstract test class for Admin/Product/AddSimpleProduct module
 *
 * @author Magento Inc.
 */

class Frontend_Checkout_SignIn extends Test_Frontend_Checkout_Abstract
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
     * Tests checkout as a Guest from FrontEnd
     *
     */

    function testMultiShippingRegisterCheckout() {
        // Test Dara
        $paramArray = array (
            "firstName" => Core::getEnvConfig('frontend/checkout/mulitiShippingRegister/firstName'),
            "lastName" => Core::getEnvConfig('frontend/checkout/mulitiShippingRegister/lastName'),
            "password" =>  Core::getEnvConfig('frontend/checkout/mulitiShippingRegister/password'),
            "email" => Core::getEnvConfig('frontend/checkout/mulitiShippingRegister/email'),
            "company" => Core::getEnvConfig('frontend/checkout/company'),
            "productUrl" => Core::getEnvConfig('frontend/checkout/mulitiShippingRegister/productUrl'),
            "qty" => Core::getEnvConfig('frontend/checkout/mulitiShippingRegister/qty'),
            "street1" => Core::getEnvConfig('frontend/checkout/street1'),
            "street2" => Core::getEnvConfig('frontend/checkout/street2'),
            "city" => Core::getEnvConfig('frontend/checkout/city'),
            "country" => Core::getEnvConfig('frontend/checkout/country'),
            "region" => Core::getEnvConfig('frontend/checkout/region'),
            "postcode" => Core::getEnvConfig('frontend/checkout/postcode'),
            "telephone" =>  Core::getEnvConfig('frontend/checkout/telephone'),
            "fax" =>  Core::getEnvConfig('frontend/checkout/fax'),
        );
        $this->multiShippingRegisterCheckout($paramArray);
    }
}
