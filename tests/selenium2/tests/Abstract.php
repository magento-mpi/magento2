<?php
/**
 * Abstract test class derived from PHPUnit_Extensions_SeleniumTestCase
 *
 * @author Magento Inc.
 */
abstract class Test_Abstract extends PHPUnit_Extensions_SeleniumTestCase
{
    /**
     * Base URL
     *
     * @var string
     */
    protected $_baseUrl = '';

    /**
     * User name
     *
     * @var string
     */
    protected $_userName = '';

    /**
     * User password
     * 
     * @var string
     */
    protected $_password = '';

    /**
     * Test ID
     * 
     * @var string
     */
    protected $_testId = "";

    /**
     * Helper local instance
     *
     * @var Helper_Admin
     */
    protected $_helper = null;

    /**
     * Add an error to the stack
     * 
     * @param string $error 
     */
    function setVerificationErrors($error)
    {
        array_push($this->verificationErrors, $error);
    }


    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp()
    {
        $this->setBrowser(Core::getEnvConfig('browser'));
        $this->setBrowserUrl(Core::getEnvConfig('frontend/baseUrl'));
        $this->_testId = strtoupper(get_class($this));

        Core::setContext($this);       
    }

    function tearDown()
    {
        parent::tearDown();
    }

}

