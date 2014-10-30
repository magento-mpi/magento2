<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Resource;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Service\V1\Data\AddressConverter;

/**
 * Integration test for service layer \Magento\Customer\Service\V1\CustomerAddressService
 *
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class AddressRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Customer\Api\AddressRepositoryInterface */
    private $_service;

    /** @var \Magento\Framework\ObjectManager */
    private $_objectManager;

    /** @var \Magento\Customer\Service\V1\Data\Address[] */
    private $_expectedAddresses;

    /** @var \Magento\Customer\Service\V1\Data\AddressBuilder */
    private $_addressBuilder;

    protected function setUp()
    {
        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_service = $this->_objectManager->create('Magento\Customer\Api\AddressRepositoryInterface');

        $this->_addressBuilder = $this->_objectManager->create('Magento\Customer\Api\Data\AddressDataBuilder');

        $builder = $this->_objectManager->create('Magento\Customer\Model\Data\RegionBuilder');
        var_dump(get_class($builder));
        $region = $builder->setRegionCode('AL')
            ->setRegion('Alabama')
            ->setRegionId(1)
            ->create();

        $this->_addressBuilder
            ->setId(1)
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setPostcode('75477')
            ->setRegion($region)
            ->setStreet(array('Green str, 67'))
            ->setTelephone('3468676')
            ->setCity('CityM')
            ->setFirstname('John')
            ->setLastname('Smith')
            ->setCompany('CompanyName');
        $address = $this->_addressBuilder->create();

        $this->_addressBuilder
            ->setId(2)
            ->setCustomerId(2)
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setPostcode('47676')
            ->setRegion($region)
            ->setStreet(array('Black str, 48'))
            ->setCity('CityX')
            ->setTelephone('3234676')
            ->setFirstname('John')
            ->setLastname('Smith');

        $address2 = $this->_addressBuilder->create();

        $this->_expectedAddresses = array($address, $address2);
    }

    protected function tearDown()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Customer\Model\AddressRegistry $addressRegistry */
        $customerRegistry = $objectManager->get('Magento\Customer\Model\CustomerRegistry');
        $customerRegistry->remove(1);
    }


    /**
     * @magentoDataFixture  Magento/Customer/_files/customer.php
     * @magentoDataFixture  Magento/Customer/_files/customer_address.php
     * @magentoAppIsolation enabled
     */
    public function testSaveNewAddress()
    {
        $this->markTestSkipped('Should be fixed in scope of MAGETWO-29651');
        $this->_addressBuilder->populate($this->_expectedAddresses[1])->setId(null);
        $proposedAddress = $this->_addressBuilder->create();
        $customerId = 1;
        $createdAddress = $this->_service->save($proposedAddress);
        $addresses = $this->_service->get($customerId);
        $this->assertEquals($this->_expectedAddresses[0], $addresses[0]);
        $expectedNewAddressBuilder = $this->_addressBuilder->populate($this->_expectedAddresses[1]);
        $expectedNewAddressBuilder->setId($addresses[1]->getId());
        $expectedNewAddress = $expectedNewAddressBuilder->create();
        $this->assertEquals(
            AddressConverter::toFlatArray($expectedNewAddress),
            AddressConverter::toFlatArray($addresses[1])
        );
    }
}
