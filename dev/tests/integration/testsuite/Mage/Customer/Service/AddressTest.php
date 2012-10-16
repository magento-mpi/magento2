<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests Mage_Customer_Service_Address
 */
class Mage_Customer_Service_AddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test for Mage_Customer_Service_Address::getByCustomerId with no addresses.
     *
     * @magentoDataFixture Mage/Customer/_files/customer.php
     */
    public function testGetByCustomerIdNoAddresses()
    {
        $service = new Mage_Customer_Service_Address;
        $addresses = $service->getByCustomerId(1);
        $this->assertInternalType('array', $addresses);
        $this->assertEmpty($addresses);
    }

    /**
     * Test for Mage_Customer_Service_Address::getByCustomerId with existing address.
     *
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @magentoDataFixture Mage/Customer/_files/customer_address.php
     */
    public function testGetByCustomerIdWithAddress()
    {
        $service = new Mage_Customer_Service_Address;
        $addresses = $service->getByCustomerId(1);
        $this->assertInternalType('array', $addresses);
        $this->assertCount(1, $addresses);
        $address = array_shift($addresses);
        $this->assertInstanceOf('Mage_Customer_Model_Address', $address);
        $this->assertAttributeContains(3468676, '_data', $address);
    }

    /**
     * Test for Mage_Customer_Service_Address::getByCustomerId with existing multiple addresses.
     *
     * @magentoDataFixture Mage/Customer/_files/customer.php
     * @magentoDataFixture Mage/Customer/_files/customer_two_addresses.php
     */
    public function testGetByCustomerIdWithTwoAddresses()
    {
        $service = new Mage_Customer_Service_Address;
        $addresses = $service->getByCustomerId(1);
        $this->assertInternalType('array', $addresses);
        $this->assertCount(2, $addresses);
        $address = array_shift($addresses);
        $this->assertInstanceOf('Mage_Customer_Model_Address', $address);
        $address = array_shift($addresses);
        $this->assertInstanceOf('Mage_Customer_Model_Address', $address);
    }
}
