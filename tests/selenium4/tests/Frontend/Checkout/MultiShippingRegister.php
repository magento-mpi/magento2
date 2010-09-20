<?php

class Frontend_multiShipping_Register_Checkout extends TestCaseAbstract
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
    function testMSRegisterCheckout()
    {
        // Test Dara
        $paramArray = Core::getEnvConfig('frontend/checkout');
        $paramArray['firstName'] =  Core::getEnvConfig('frontend/checkout/mulitiShippingRegister/firstName');
        $paramArray['lastName'] =  Core::getEnvConfig('frontend/checkout/mulitiShippingRegister/lastName');
        $paramArray['email'] =  Core::getEnvConfig('frontend/checkout/mulitiShippingRegister/email');
        $paramArray['password'] =  Core::getEnvConfig('frontend/checkout/mulitiShippingRegister/password');
        $paramArray['qty'] =  Core::getEnvConfig('frontend/checkout/mulitiShippingRegister/qty');
        //Test Flow
        $this->model->multiShippingRegisterCheckout($paramArray);
    }
}
