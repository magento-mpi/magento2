<?php

class Frontend_Checkout_Guest extends TestCaseAbstract
{
    /**
     * Setup procedure.
     * Initializes model and loads configuration
     */
    function setUp() {
        $this->model = $this->getModel('frontend/checkout');
        $this->setUiNamespace();
    }

    /**
     * Test frontend guest checkout
     */
    function testGuestCheckout()
    {
        // Test Dara
        $paramArray = Core::getEnvConfig('frontend/checkout');
        $paramArray['firstName'] =  Core::getEnvConfig('frontend/checkout/guest/firstName');
        $paramArray['lastName'] =  Core::getEnvConfig('frontend/checkout/guest/lastName');
        $paramArray['email'] =  Core::getEnvConfig('frontend/checkout/guest/email');
        $paramArray['password'] =  Core::getEnvConfig('frontend/checkout/guest/password');

        //Test Flow
        $this->model->guestCheckout($paramArray);
    }
}
