<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

use Magento\Exception\InputException;
use Magento\Exception\NoSuchEntityException;

/**
 * Integration test for service layer \Magento\Customer\Service\V1\CustomerService
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class CustomerServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var CustomerServiceInterface */
    private $_service;

    /** @var CustomerAccountServiceInterface Needed for password checking */
    private $_accountService;

    /** @var CustomerAddressServiceInterface Needed for verifying if addresses are deleted */
    private $_addressService;

    /** @var \Magento\ObjectManager */
    private $_objectManager;

    /** @var \Magento\Customer\Service\V1\Dto\Address[] */
    private $_expectedAddresses;

    /** @var \Magento\Customer\Service\V1\Dto\AddressBuilder */
    private $_addressBuilder;

    /** @var \Magento\Customer\Service\V1\Dto\CustomerBuilder */
    private $_customerBuilder;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_service = $this->_objectManager->create('Magento\Customer\Service\V1\CustomerServiceInterface');
        $this->_accountService = $this->_objectManager
            ->create('Magento\Customer\Service\V1\CustomerAccountServiceInterface');
        $this->_addressService = $this->_objectManager
            ->create('Magento\Customer\Service\V1\CustomerAddressServiceInterface');

        $this->_addressBuilder = $this->_objectManager->create('Magento\Customer\Service\V1\Dto\AddressBuilder');
        $this->_customerBuilder = $this->_objectManager->create('Magento\Customer\Service\V1\Dto\CustomerBuilder');

        $this->_addressBuilder->setId(1)
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setDefaultBilling(true)
            ->setDefaultShipping(true)
            ->setPostcode('75477')
            ->setRegion(new Dto\Region([
                'region_code' => 'AL',
                'region' => 'Alabama',
                'region_id' => 1
            ]))
            ->setStreet(['Green str, 67'])
            ->setTelephone('3468676')
            ->setCity('CityM')
            ->setFirstname('John')
            ->setLastname('Smith');
        $address = $this->_addressBuilder->create();

        /* XXX: would it be better to have a clear method for this? */
        $this->_addressBuilder->setId(2)
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setDefaultBilling(false)
            ->setDefaultShipping(false)
            ->setPostcode('47676')
            ->setRegion(new Dto\Region([
                'region_code' => 'AL',
                'region' => 'Alabama',
                'region_id' => 1
            ]))
            ->setStreet(['Black str, 48'])
            ->setCity('CityX')
            ->setTelephone('3234676')
            ->setFirstname('John')
            ->setLastname('Smith');
        $address2 = $this->_addressBuilder->create();

        $this->_expectedAddresses = [$address, $address2];
    }

    /**
     * @param mixed $custId
     * @dataProvider invalidCustomerIdsDataProvider
     * @expectedException \Magento\Exception\NoSuchEntityException
     * @expectedExceptionMessage customerId
     */
    public function testInvalidCustomerIds($custId)
    {
        $this->_accountService->getCustomer($custId);
    }

    public function invalidCustomerIdsDataProvider()
    {
        return array(
            array('ab'),
            array(' '),
            array(-1),
            array(0),
            array(' 1234'),
            array('-1'),
            array('0'),
        );
    }

    /**
     * @magentoAppArea adminhtml
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppIsolation enabled
     * @expectedException \Magento\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with customerId = 1
     */
    public function testDeleteCustomer()
    {
        // _files/customer.php sets the customer id to 1
        $this->_service->deleteCustomer(1);
        $this->_accountService->getCustomer(1);
    }

    /**
     *
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_two_addresses.php
     * @expectedException \Magento\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with customerId = 1
     */
    public function testDeleteCustomerWithAddress()
    {
        $this->markTestSkipped('Investigate how to ensure that addresses are deleted. Currently it is false negative');
        //Verify address is created for the customer;
        $result = $this->_addressService->getAddresses(1);
        $this->assertEquals(2, count($result));
        // _files/customer.php sets the customer id to 1
        $this->_service->deleteCustomer(1);

        // Verify by directly loading the address by id
        $this->verifyDeletedAddress(1);
        $this->verifyDeletedAddress(2);

        //Verify by calling the Address Service. This will throw the expected exception since customerId doesn't exist
        $result = $this->_addressService->getAddresses(1);
        $this->assertTrue(empty($result));
    }

    /**
     * Check if the Address with the give addressid is deleted
     *
     * @param int $addressId
     */
    protected function verifyDeletedAddress($addressId)
    {
        /** @var $addressFactory \Magento\Customer\Model\AddressFactory */
        $addressFactory = $this->_objectManager
            ->create('Magento\Customer\Model\AddressFactory');
        $addressModel = $addressFactory->create()->load($addressId);
        $addressData = $addressModel->getData();
        $this->assertTrue(empty($addressData));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoAppIsolation enabled
     * @expectedException
     * V1\Exception
     * @expectedExceptionMessage Cannot complete this operation from non-admin area.
     */
    public function testDeleteCustomerNonSecureArea()
    {
        /** _files/customer.php sets the customer id to 1 */
        $this->_service->deleteCustomer(1);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetCustomerByEmail()
    {
        $websiteId = 1;
        /** _files/customer.php sets the customer with id = 1 and email = customer@example.com */
        $customer = $this->_service->getCustomerByEmail('customer@example.com', $websiteId);
        $this->assertEquals(1, $customer->getCustomerId());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @expectedException \Magento\Core\Exception
     * @expectedExceptionMessage Customer website ID must be specified when using the website scope
     */
    public function testGetCustomerByEmailNoWebsiteSpecified()
    {
        /** _files/customer.php sets the customer with id = 1 and email = customer@example.com */
        $this->_service->getCustomerByEmail('customer@example.com');
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @expectedException \Magento\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with email = nonexistent@example.com
     */
    public function testGetCustomerByEmailNonExistentEmail()
    {
        $websiteId = 1;
        /** _files/customer.php sets the customer with id = 1 and email = customer@example.com */
        $customer = $this->_service->getCustomerByEmail('nonexistent@example.com', $websiteId);
        assertEquals(null, $customer->getCustomerId());
    }
}

