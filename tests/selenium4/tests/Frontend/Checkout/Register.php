<?php

class Frontend_Register_Guest extends TestCaseAbstract
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
     * Test frontend checkout with registration
     */
    function testRegisterCheckout()
    {
        // Test Dara
        $paramArray = Core::getEnvConfig('frontend/checkout');
        $paramArray['firstName'] =  Core::getEnvConfig('frontend/checkout/register/firstName');
        $paramArray['lastName'] =  Core::getEnvConfig('frontend/checkout/register/lastName');
        $paramArray['email'] =  Core::getEnvConfig('frontend/checkout/register/email');
        $paramArray['password'] =  Core::getEnvConfig('frontend/checkout/register/password');

        //Test Flow
        $this->model->registerCheckout($paramArray);
    }
}
