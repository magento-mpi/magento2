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

        $this->_addressBuilder = $this->_objectManager->create('Magento\Customer\Model\Data\AddressBuilder');

        $builder = $this->_objectManager->create('\Magento\Customer\Model\Data\RegionBuilder');
        $region = $builder
            ->setRegionCode('AL')
            ->setRegion('Alabama')
            ->setRegionId(1)
            ->create();

        $this->_addressBuilder
            ->setId(1)
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setDefaultBilling(true)
            ->setDefaultShipping(true)
            ->setPostcode('75477')
            ->setRegion($region)
            ->setStreet(array('Green str, 67'))
            ->setTelephone('3468676')
            ->setCity('CityM')
            ->setFirstname('John')
            ->setLastname('Smith')
            ->setCompany('CompanyName');
        $address = $this->_addressBuilder->create();

        /* XXX: would it be better to have a clear method for this? */
        $this->_addressBuilder
            ->setId(2)
            ->setCustomerId(2)
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setDefaultBilling(false)
            ->setDefaultShipping(false)
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
        $this->_addressBuilder->populate($this->_expectedAddresses[1])->setId(null);
        $proposedAddress = $this->_addressBuilder->create();
        $customerId = 1;

        var_dump($proposedAddress);

        $createdAddress = $this->_service->save($proposedAddress);

        var_dump($createdAddress);

//        $addresses = $this->_service->get($customerId);
//        $this->assertEquals($this->_expectedAddresses[0], $addresses[0]);
//        $expectedNewAddressBuilder = $this->_addressBuilder->populate($this->_expectedAddresses[1]);
//        $expectedNewAddressBuilder->setId($addresses[1]->getId());
//        $expectedNewAddress = $expectedNewAddressBuilder->create();
//        $this->assertEquals(
//            AddressConverter::toFlatArray($expectedNewAddress),
//            AddressConverter::toFlatArray($addresses[1])
//        );
    }

    /**
     * Helper function that returns an Address Data Object that matches the data from customer_address fixture
     *
     * @return \Magento\Customer\Service\V1\Data\AddressBuilder
     */
    private function _createFirstAddressBuilder()
    {
        $addressBuilder = $this->_addressBuilder->populate($this->_expectedAddresses[0]);
        $addressBuilder->setId(null);
        return $addressBuilder;
    }

    /**
     * Checks that the arrays are equal, but accounts for the 'region' being an object
     *
     * @param array $expectedArray
     * @param array $actualArray
     */
    protected function _assertAddressAndRegionArrayEquals($expectedArray, $actualArray)
    {
        if (array_key_exists('region', $expectedArray)) {
            /** @var \Magento\Customer\Service\V1\Data\Region $expectedRegion */
            $expectedRegion = $expectedArray['region'];
            unset($expectedArray['region']);
        }
        if (array_key_exists('region', $actualArray)) {
            /** @var \Magento\Customer\Service\V1\Data\Region $actualRegion */
            $actualRegion = $actualArray['region'];
            unset($actualArray['region']);
        }

        $this->assertEquals($expectedArray, $actualArray);

        // Either both set or both unset
        $this->assertTrue(!(isset($expectedRegion) xor isset($actualRegion)));
        if (isset($expectedRegion) && isset($actualRegion)) {
            $this->assertTrue(is_array($expectedRegion));
            $this->assertTrue(is_array($actualRegion));
            $this->assertEquals($expectedRegion, $actualRegion);
        }
    }
}
