<?php
/**
 * Abstract test class derived from PHPUnit_Extensions_SeleniumTestCase
 *
 * @author Magento Inc.
 */
abstract class Test_Admin_Customer_Abstract extends Test_Admin_Abstract
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
    protected $_username = '';

    /**
     * User password
     * 
     * @var string
     */
    protected $_password = '';

    /**
     * Customer ID
     * 
     * @var int
     */
    protected $_customerId;

    /**
     * Test ID
     * 
     * @var string
     */
    protected $_testId;

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
        $this->setBrowser("*chrome");
        $this->setBrowserUrl("http://kq.varien.com/");
        $this->_admincustomeraddresshelper = new AdminCustomerAddressHelper($this);

        // Get test parameters....
//        $this->_baseurl = "http://kq.varien.com/enterprise/1.8.0.0/index.php/control/index/";
//        $this->_username = "admin";
//        $this->_password = "123123q";
        $this->_testId = strtoupper(get_class($this));
    }

}

