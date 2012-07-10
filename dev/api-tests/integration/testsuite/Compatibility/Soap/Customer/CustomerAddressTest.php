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
class Compatibility_Soap_CustomerAddressTest extends Compatibility_Soap_SoapAbstract
{
    /**
     * Customer Address ID created at previous API
     * @var int
     */
    protected static $_prevCustomerAddressId;

    /**
     * Customer Address ID created at current API
     * @var int
     */
    protected static $_currCustomerAddressId;

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
     * Test customer address create method compatibility.
     * Scenario:
     * 1. Create customer address at previous API.
     * 1. Create customer address at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     */
    public function testCustomerAddressCreate()
    {
        $apiMethod = 'customer_address.create';
        $customerIds = $this->_createCustomers();
        self::$_prevCustomerId = $customerIds['prevCustomerId'];
        self::$_currCustomerId = $customerIds['currCustomerId'];
        $addressData = array(
            'firstname' => 'John',
            'lastname' => 'Doe',
            'street' => array('Street line 1', 'Street line 2'),
            'city' => 'Weaverville',
            'country_id' => 'US',
            'region' => 'Texas',
            'region_id' => 3,
            'postcode' => '96093',
            'telephone' => '530-623-2513',
            'is_default_billing' => FALSE,
            'is_default_shipping' => FALSE
        );
        self::$_prevCustomerAddressId = $this->prevCall($apiMethod, array('customerId' => self::$_prevCustomerId, 'addressdata' => $addressData));
        self::$_currCustomerAddressId = $this->currCall($apiMethod, array('customerId' => self::$_currCustomerId, 'addressdata' => $addressData));
        $this->_checkVersionType(self::$_prevCustomerAddressId, self::$_currCustomerAddressId, $apiMethod);
    }

    /**
     * Test customer list method compatibility.
     * Scenario:
     * 1. Get customer address list at previous API.
     * 2. Get customer address list at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCustomerAddressCreate
     */
    public function testCustomerAddressList()
    {
        $apiMethod = 'customer_address.list';
        $prevResponse = $this->prevCall($apiMethod, self::$_prevCustomerId);
        $currResponse = $this->currCall($apiMethod, self::$_currCustomerId);
        $this->_checkResponse($prevResponse, $currResponse, $apiMethod);
        $this->_checkVersionSignature($prevResponse[0], $currResponse[0], $apiMethod);
    }

    /**
     * Test customer address info method compatibility.
     * Scenario:
     * 1. Get customer address info, created in testCustomerAddressCreate, at previous API.
     * 2. Get customer address info, created in testCustomerAddressCreate, at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCustomerAddressCreate
     */
    public function testCustomerAddressInfo()
    {
        $apiMethod = 'customer_address.info';
        $prevResponse = $this->prevCall($apiMethod, self::$_prevCustomerAddressId);
        $currResponse = $this->currCall($apiMethod, self::$_currCustomerAddressId);
        $this->_checkResponse($prevResponse, $currResponse, $apiMethod);
        $this->_checkVersionSignature($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test customer address update method compatibility.
     * Scenario:
     * 1. Update customer address, created in testCustomerAddressCreate, at previous API.
     * 1. Update customer address, created in testCustomerAddressCreate, at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCustomerAddressCreate
     */
    public function testCustomerAddressUpdate()
    {
        $apiMethod = 'customer_address.update';
        $addressData = array(
            'firstname' => 'John',
            'lastname' => 'Doe',
            'street' => array('Street line 1 Updated', 'Street line 2 Updated'),
            'city' => 'Weaverville',
            'country_id' => 'US',
            'region' => 'Texas',
            'region_id' => 3,
            'postcode' => '96093',
            'telephone' => '530-623-2513',
            'is_default_billing' => TRUE,
            'is_default_shipping' => TRUE
        );
        $prevResponse = $this->prevCall($apiMethod, self::$_prevCustomerAddressId, $addressData);
        $currResponse = $this->currCall($apiMethod, self::$_currCustomerAddressId, $addressData);
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }

    /**
     * Test customer address delete method compatibility.
     * Scenario:
     * 1. Delete customer address, created in testCustomerAddressCreate, at previous API.
     * 2. Delete customer address, created in testCustomerAddressCreate, at current API.
     * Expected result:
     * No errors raised and type of current API response is the same as in previous.
     *
     * @depends testCustomerAddressCreate
     */
    public function testCustomerAddressDelete()
    {
        $apiMethod = 'customer_address.delete';
        $prevResponse = $this->prevCall($apiMethod, self::$_prevCustomerAddressId);
        $currResponse = $this->currCall($apiMethod, self::$_currCustomerAddressId);
        $this->_checkVersionType($prevResponse, $currResponse, $apiMethod);
    }
}
