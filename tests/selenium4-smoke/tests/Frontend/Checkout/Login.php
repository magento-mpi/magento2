<?php

class Frontend_Login_Guest extends TestCaseAbstract
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
     * Test frontend checkout with log in
     */
    function testLoginCheckout()
    {
        // Test Dara
        $paramArray = Core::getEnvConfig('frontend/checkout');
        $paramArray['email'] =  Core::getEnvConfig('frontend/checkout/register/email');
        $paramArray['password'] =  Core::getEnvConfig('frontend/checkout/register/password');

        //Test Flow
        $this->model->loginCheckout($paramArray);
    }
}
