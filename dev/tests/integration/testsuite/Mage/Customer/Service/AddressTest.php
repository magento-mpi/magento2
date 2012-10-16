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
     * Test for Mage_Customer_Service_Address::getByCustomerId
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
}
