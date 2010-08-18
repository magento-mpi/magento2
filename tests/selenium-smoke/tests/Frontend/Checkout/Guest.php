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
            "email" => Core::getEnvConfig('frontend/checkout/email'),
            "address1" => Core::getEnvConfig('frontend/checkout/address1'),
            "address2" => Core::getEnvConfig('frontend/checkout/address2'),
            "city" => Core::getEnvConfig('frontend/checkout/city'),
            "region" => Core::getEnvConfig('frontend/checkout/region'),
            "postcode" => Core::getEnvConfig('frontend/checkout/postcode'),
            "phone" =>  Core::getEnvConfig('frontend/checkout/phone'),
        );
        $this->guestCheckout($paramArray);
        sleep(10);
    }
}
?>
