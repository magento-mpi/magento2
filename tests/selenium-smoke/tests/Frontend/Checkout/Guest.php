<?php
/**
 * Abstract test class for Admin/Product/AddSimpleProduct module
 *
 * @author Magento Inc.
 */

class Frontend_Checkout_Guest extends Test_Frontend_Checkout_Abstract
{

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp()
    {
        parent::setUp();

        // Get test parameters
    }

    /**
     * Tests checkout as a Guest from FrontEnd
     *     
     */

    function testGuestCheckout()
    {
        // Test Dara
        $paramArray = array (
            "productUrl" => Core::getEnvConfig('frontend/checkout/productUrl'),
            "qty" => Core::getEnvConfig('frontend/checkout/qty'),
            "firstName" => Core::getEnvConfig('frontend/checkout/guest/firstName'),
            "lastName" => Core::getEnvConfig('frontend/checkout/guest/lastName'),
            "company" => Core::getEnvConfig('frontend/checkout/company'), 
            "email" => Core::getEnvConfig('frontend/checkout/guest/email'),
            "street1" => Core::getEnvConfig('frontend/checkout/street1'),
            "street2" => Core::getEnvConfig('frontend/checkout/street2'),
            "city" => Core::getEnvConfig('frontend/checkout/city'),
            "country" => Core::getEnvConfig('frontend/checkout/country'),
            "region" => Core::getEnvConfig('frontend/checkout/region'),
            "postcode" => Core::getEnvConfig('frontend/checkout/postcode'),
            "telephone" =>  Core::getEnvConfig('frontend/checkout/telephone'),
            "fax" =>  Core::getEnvConfig('frontend/checkout/fax'),
        );
        $this->guestCheckout($paramArray);
    }
}
?>
