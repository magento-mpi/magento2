<?php
/**
 * Test for \Magento\Customer\Model\AddressRegistry
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model;

class AddressRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\AddressRegistry
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\AddressRegistry');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testRetrieve()
    {
        $addressId = 1;
        $address = $this->_model->retrieve($addressId);
        $this->assertInstanceOf('\Magento\Customer\Model\Address', $address);
        $this->assertEquals($addressId, $address->getId());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     */
    public function testRetrieveCached()
    {
        $addressId = 1;
        $addressBeforeDeletion = $this->_model->retrieve($addressId);
        $address2 = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Address');
        $address2->load($addressId)
            ->delete();
        $addressAfterDeletion = $this->_model->retrieve($addressId);
        $this->assertEquals($addressBeforeDeletion, $addressAfterDeletion);
        $this->assertInstanceOf('\Magento\Customer\Model\Address', $addressAfterDeletion);
        $this->assertEquals($addressId, $addressAfterDeletion->getId());
    }

    /**
     * @expectedException \Magento\Exception\NoSuchEntityException
     */
    public function testRetrieveException()
    {
        $addressId = 1;
        $address = $this->_model->retrieve($addressId);
        $this->assertInstanceOf('\Magento\Customer\Model\Address', $address);
        $this->assertEquals($addressId, $address->getId());
    }

    /**
     * @expectedException \Magento\Exception\NoSuchEntityException
     */
    public function testRemove()
    {
        $addressId = 1;
        $address = $this->_model->retrieve($addressId);
        $this->assertInstanceOf('\Magento\Customer\Model\Address', $address);
        $address->delete();
        $this->_model->remove($addressId);
        $this->_model->retrieve($addressId);
    }
}