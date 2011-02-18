<?php

class Frontend_Register_Configurable_Positive_Checkout extends TestCaseAbstract
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
     * Test frontend checkout with registration
     */
    function testRegisterConfigurablePositive()
    {
        // Test Dara
        $paramArray = array (
            //product data
            'baseUrl' => 'http://kq.varien.com/builds/ee-nightly/current/websites/smoke',
            'categoryName' => 'SL-Category/Base',
            //'categoryName' => 'SL-Category/wCO',
            'productName' => 'Configurable Product - Base',
            'configOptions' => array (
                                    'attribforconfig' => 'Option1',
                                    ),
            'qty' => 1,
            //checkout data
            'checkoutMethod' => 'Register',
            'shippingMethod' => 'Flat',
            'paymentMethod' => 'Check / Money order',
            //customer data
            'firstName' => 'Login',
            'lastName' => 'User',
            'email' => 'stu1@varien.com',
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
        if ($this->modelProduct->doOpen($paramArray)) {
            if ($this->modelProduct->placeToCart($paramArray)) {
                $this->modelShoppingCart->proceedCheckout();
                $this->modelCheckout->doCheckout($paramArray);
            } else {
                $this->setVerificationErrors('Product was not placed to cart');
            }
        } else {
            $this->setVerificationErrors('Product could not be opened');
        }
    }
}
