<?php

class Frontend_Checkout_Guest_Grouped extends TestCaseAbstract
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
     * MAGE-39:Performs Guest checkout of Groupped product.
     */
    function testGuestGroupedPositive()
    {
        // Test Dara
        $paramArray = array (
            //product data
            'baseUrl' => 'http://kq.varien.com/builds/ee-nightly/current/websites/smoke',
            'categoryName' => 'SL-Category/Base',
            'productName' => 'Grouped Product - Base',
            'associatedProducts' => array (
                                    'A Product - A' => '3',
                                    'A Product - B' => '2',
                                    ),
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
        sleep(10);
        $this->modelProduct->placeToCart($paramArray);
        $this->modelShoppingCart->proceedCheckout();
        $this->modelCheckout->doCheckout($paramArray);
    }
}
