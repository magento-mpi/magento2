<?php
class Frontend_Register extends Test_Frontend_Abstract
{

    /**
     * Check registration from Front
     *
     */

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp()
    {
        parent::setUp();
    }

    /**
     * Test registration from FronEnd
     *
     */
    function testFrontRegister()
    {
        // Test Dara
        $paramArray = array (
            "firstName" => Core::getEnvConfig('frontend/auth/firstname'),
            "lastName" => Core::getEnvConfig('frontend/auth/lastname'),
            "email" =>  Core::getEnvConfig('frontend/auth/email'),
            "password" =>  Core::getEnvConfig('frontend/auth/password')
        );
        //Test Flow
        $this->frontRegister($paramArray);
    }
}