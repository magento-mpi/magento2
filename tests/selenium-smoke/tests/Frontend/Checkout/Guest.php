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
    function setUp() {
        parent::setUp();

        // Get test parameters
    }

    /**
     * Test addion new sub category to the $StoreView store view
     *
     *@param sku
     *@param productName
     *@param categoryName
     *@param webSiteName
     *@param storeViewName
     */

    function testGuestCheckout() {
        // Test Dara
        $paramArray = array (
            "productUrl" => Core::getEnvConfig('frontend/checkout/productUrl'),
            "qty" => Core::getEnvConfig('frontend/checkout/qty'),
            "firstName" => Core::getEnvConfig('frontend/checkout/firstName'),
            "lastName" => Core::getEnvConfig('frontend/checkout/lastName'),
            "company" => Core::getEnvConfig('frontend/checkout/company'), 
            "email" => Core::getEnvConfig('frontend/checkout/email'),
            "street1" => Core::getEnvConfig('frontend/checkout/street1'),
            "street2" => Core::getEnvConfig('frontend/checkout/street2'),
            "city" => Core::getEnvConfig('frontend/checkout/city'),
            "country" => Core::getEnvConfig('frontend/checkout/country2'),
            "region" => Core::getEnvConfig('frontend/checkout/region'),
            "postcode" => Core::getEnvConfig('frontend/checkout/postcode'),
            "telephone" =>  Core::getEnvConfig('frontend/checkout/telephone'),
            "fax" =>  Core::getEnvConfig('frontend/checkout/fax'),
        );
        $this->guestCheckout($paramArray);
        sleep(5);
    }
}
?>
