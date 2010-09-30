<?php

class Frontend_Checkout_Guest_Virtual extends TestCaseAbstract
{
    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->modelProduct = $this->getModel('frontend/product');
        $this->modelShoppingCart = $this->getModel('frontend/shoppingcart');
        $this->modelCheckout = $this->getModel('frontend/checkout');
        $this->setUiNamespace();
    }

    /**
     * Test frontend guest checkout
     * MAGE-37:Performs Guest checkout of Simple product
     */
    function testGuestVirtualPositive()
    {
        // Test Dara
        $paramArray = array (
            //product data
            'baseUrl' => 'http://kq.varien.com/builds/ee-nightly/current/websites/smoke',
            'categoryName' => 'SL-Category/Base',
            //'categoryName' => 'SL-Category/wCO',
            'productName' => 'Virtual Product - Base',
            'qty' => 1,
            //checkout data
            'checkoutMethod' => 'Checkout as Guest',
            'shippingMethod' => 'Flat',
            'paymentMethod' => 'Check / Money order',
            //customer data
            'firstName' => 'Guest',
            'lastName' => 'User',
            'email' => 'atu1@varien.com',
            'password' => '123123',
            'company' => 'AT Company',
            'street1' => 'street1',
            'street2' => 'street2',
            'city' => 'AT City',
            'country' => 'United States',
            'region' => 'Texas',
            'postcode' => '900034',
            'telephone' => '5555555',
            'fax' => '5555556'
        );

        //Test Flow
        $this->modelProduct->doOpen($paramArray);
        $this->modelProduct->placeToCart($paramArray);
        sleep(20);
        $this->modelShoppingCart->proceedCheckout();
        $this->modelCheckout->doCheckout($paramArray);
        $errorsStackWasChanged = true;
        while ($errorsStackWasChanged) :
        {
            $error = $this->getLastVerificationError();
            $errorsStackWasChanged = false;
            $this->printDebug($error);
            if (strpos($error, "Shipping method tab is not visible")) {
                $this->popVerificationErrors();
                $errorsStackWasChanged = true;
            }
        };
        endwhile;

    }
}
