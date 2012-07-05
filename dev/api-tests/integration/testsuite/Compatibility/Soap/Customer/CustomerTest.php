<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  compatibility_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Product methods compatibility between previous and current API versions.
 */
class Compatibility_Soap_CustomerTest extends Compatibility_Soap_SoapAbstract
{
    /**
     * Customer ID created at previous API
     * @var int
     */
    protected static $_prevCustomerId;

    /**
     * Customer ID created at current API
     * @var int
     */
    protected static $_currCustomerId;

    /**
     * Test product attribute set create method compatibility.
     * Scenario:
     * 1. Create product attribute set at previous API.
     * 2. Create product attribute set at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     */
    public function testCustomerCreate()
    {
        $apiMethod = 'customer.create';
        $customerIds = $this->_createCustomers();
        self::$_prevCustomerId = $customerIds['prevCustomerId'];
        self::$_currCustomerId = $customerIds['currCustomerId'];
        $this->_checkVersionType(self::$_prevCustomerId, self::$_currCustomerId, $apiMethod);
    }

    /**
     * Test customer list method compatibility.
     * Scenario:
     * 1. Get customer list at previous API.
     * 2. Get customer list at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCustomerCreate
     */
    public function testCustomerList()
    {
        $apiMethod = 'customer.list';
        $prevResponse = $this->prevCall($apiMethod);
        $currResponse = $this->currCall($apiMethod);
        $this->_checkResponse($prevResponse, $currResponse, $apiMethod);
        $this->_checkVersionSignature($prevResponse[0], $currResponse[0], $apiMethod);
    }

    /**
     * Test customer list method compatibility.
     * Scenario:
     * 1. Get customer list at previous API.
     * 2. Get customer list at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCustomerCreate
     */
    public function testCustomerInfo()
    {
        $apiMethod = 'customer.info';
        $prevResponse = $this->prevCall($apiMethod, array(self::$_prevCustomerId));
        $currResponse = $this->currCall($apiMethod, array(self::$_currCustomerId));
        $this->_checkVersionSignature($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test product attribute set create method compatibility.
     * Scenario:
     * 1. Create product attribute set at previous API.
     * 2. Create product attribute set at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCustomerCreate
     */
    public function testCustomerUpdate()
    {
        $apiMethod = 'customer.update';
        $customerData = array(array(
//            'email' => 'customer-mail' . uniqid() . '@example.org',
            'firstname' => 'Test Name Updated',
            'lastname' => 'Test Last Name Updated',
            'password' => 'password',
            'website_id' => 1,
            'store_id' => 1,
            'group_id' => 1
        ));
        $prevResponse = $this->prevCall($apiMethod, self::$_prevCustomerId, $customerData);
        $currResponse = $this->currCall($apiMethod, self::$_currCustomerId, $customerData);
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test customer list method compatibility.
     * Scenario:
     * 1. Get customer list at previous API.
     * 2. Get customer list at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCustomerCreate
     */
    public function testCustomerDelete()
    {
        $apiMethod = 'customer.delete';
        $prevResponse = $this->prevCall($apiMethod, self::$_prevCustomerId);
        $currResponse = $this->currCall($apiMethod, self::$_currCustomerId);
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

}
