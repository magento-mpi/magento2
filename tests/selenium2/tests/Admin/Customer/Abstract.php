<?php
/**
 * Abstract test class derived from PHPUnit_Extensions_SeleniumTestCase
 *
 * @author Magento Inc.
 */
abstract class Test_Admin_Customer_Abstract extends Test_Admin_Abstract
{
    /**
     * Customer ID
     * 
     * @var int
     */
    protected $_customerId;
    
    /**
     * Fetches the customer id
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_customerId;
    }

    /**
     * Setup procedure.
     * Must be overriden in the children having any additional code prepended with parent::setUp();
     */
    function setUp()
    {
        parent::setUp();
        $this->_helper = Core::getHelper('admin_customer_address');
        $this->_helper -> updateContex();
        $this->_customerId = Core::getEnvConfig('backend/customer/id');
    }

}

