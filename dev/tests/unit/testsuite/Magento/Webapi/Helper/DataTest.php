<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Helper;
use Magento\Customer\Service\V1\Data\Customer;
use Magento\Customer\Service\V1\Data\RegionBuilder;

/**
 * Class implements tests for \Magento\Webapi\Helper\Data class.
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Helper\Data */
    protected $_helper;

    const CONFIRMATION = 'a4fg7h893e39d';
    const CREATED_AT = '2013-11-05';
    const CREATED_IN = 'default';
    const STORE_NAME = 'Store Name';
    const DOB = '1970-01-01';
    const GENDER = 'Male';
    const GROUP_ID = 1;
    const MIDDLENAME = 'A';
    const PREFIX = 'Mr.';
    const STORE_ID = 1;
    const SUFFIX = 'Esq.';
    const TAXVAT = '12';
    const WEBSITE_ID = 1;
    const ID = 1;
    const FIRSTNAME = 'Jane';
    const LASTNAME = 'Doe';
    const ATTRIBUTE_CODE = 'attribute_code';
    const ATTRIBUTE_VALUE = 'attribute_value';
    const REGION_CODE = 'AL';
    const REGION_ID = '1';
    const REGION = 'Alabama';

    /**
     * Set up helper.
     */
    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_helper = $objectManager->getObject('Magento\Webapi\Helper\Data');
        parent::setUp();
    }

    /**
     * Test identifying service name parts including subservices using class name.
     *
     * @dataProvider serviceNamePartsDataProvider
     */
    public function testGetServiceNameParts($className, $preserveVersion, $expected)
    {
        $actual = $this->_helper->getServiceNameParts(
            $className,
            $preserveVersion
        );
        $this->assertEquals($expected, $actual);
    }

    /**
     * Dataprovider for serviceNameParts
     *
     * @return array
     */
    public function serviceNamePartsDataProvider()
    {
        return array(
            array('Magento\Customer\Service\V1\Customer\AddressInterface', false, array('Customer', 'Address')),
            array(
                'Vendor\Customer\Service\V1\Customer\AddressInterface',
                true,
                array('VendorCustomer', 'Address', 'V1')
            ),
            array('Magento\Catalog\Service\V2\ProductInterface', true, array('CatalogProduct', 'V2'))
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @dataProvider dataProviderForTestGetServiceNamePartsInvalidName
     */
    public function testGetServiceNamePartsInvalidName($interfaceClassName)
    {
        $this->_helper->getServiceNameParts($interfaceClassName);
    }

    public function dataProviderForTestGetServiceNamePartsInvalidName()
    {
        return array(
            array('BarV1Interface'), // Missed vendor, module, 'Service'
            array('Service\\V1Interface'), // Missed vendor and module
            array('Magento\\Foo\\Service\\BarVxInterface'), // Version number should be a number
            array('Magento\\Foo\\Service\\BarInterface'), // Version missed
            array('Magento\\Foo\\Service\\BarV1'), // 'Interface' missed
            array('Foo\\Service\\BarV1Interface'), // Module missed
            array('Foo\\BarV1Interface'), // Module and 'Service' missed
        );
    }

    public function testUpackArray()
    {
        $objectManager =  new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder */
        $addressBuilder = $objectManager->getObject('Magento\Customer\Service\V1\Data\AddressBuilder');
        /** @var \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $metadataService */
        $metadataService = $this->getMockBuilder('Magento\Customer\Service\V1\CustomerMetadataService')
            ->disableOriginalConstructor()
            ->getMock();
        $metadataService->expects($this->any())
            ->method('getCustomCustomerAttributeMetadata')
            ->will($this->returnValue([]));
        /** @var \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder */
        $customerBuilder = $objectManager->getObject(
            'Magento\Customer\Service\V1\Data\CustomerBuilder',
            ['metadataService' => $metadataService]
        );
        /** @var \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder */
        $customerDetailsBuilder =
            $objectManager->getObject('Magento\Customer\Service\V1\Data\CustomerDetailsBuilder');

        $street1 = ['Green str, 67'];
        $street2 = ['Black str, 48', 'Building D'];
        $expected = [$street1, $street2];
        $addressBuilder->setId(1)
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setDefaultBilling(true)
            ->setDefaultShipping(true)
            ->setPostcode('75477')
            ->setRegion(
                (new RegionBuilder())->setRegionCode(self::REGION_CODE)->setRegion(self::REGION)
                    ->setRegionId(self::REGION_ID)->create()
            )
            ->setStreet($street1)
            ->setTelephone('3468676')
            ->setCity('CityM')
            ->setFirstname('John')
            ->setLastname('Smith');
        $address = $addressBuilder->create();

        $addressBuilder->setId(2)
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setDefaultBilling(false)
            ->setDefaultShipping(false)
            ->setPostcode('47676')
            ->setRegion(
                (new RegionBuilder())->setRegionCode(self::REGION_CODE)->setRegion(self::REGION)
                    ->setRegionId(self::REGION_ID)->create()
            )
            ->setStreet($street2)
            ->setCity('CityX')
            ->setTelephone('3234676')
            ->setFirstname('John')
            ->setLastname('Smith');
        $address2 = $addressBuilder->create();

        $customerData = [
            Customer::FIRSTNAME => self::FIRSTNAME,
            Customer::LASTNAME => self::LASTNAME,
            Customer::EMAIL => 'janedoe@example.com',
            Customer::CONFIRMATION => self::CONFIRMATION,
            Customer::CREATED_AT => self::CREATED_AT,
            Customer::CREATED_IN => self::STORE_NAME,
            Customer::DOB => self::DOB,
            Customer::GENDER => self::GENDER,
            Customer::GROUP_ID => self::GROUP_ID,
            Customer::MIDDLENAME => self::MIDDLENAME,
            Customer::PREFIX => self::PREFIX,
            Customer::STORE_ID => self::STORE_ID,
            Customer::SUFFIX => self::SUFFIX,
            Customer::TAXVAT => self::TAXVAT,
            Customer::WEBSITE_ID => self::WEBSITE_ID
        ];
        $customerData = $customerBuilder->populateWithArray($customerData)->create();
        $customerDetails = $customerDetailsBuilder->setAddresses([$address, $address2])
            ->setCustomer($customerData)
            ->create();
        $response = $this->_helper->unpackArray($customerDetails->__toArray());

        //Check if keys are correctly converted to camel case wherever necessary
        $this->assertEquals(self::FIRSTNAME, $response->customer->firstname);
        $this->assertEquals(self::GROUP_ID, $response->customer->groupId);
        foreach ($response->addresses as $key => $address) {
            $region = $address->region;
            $this->assertEquals(self::REGION, $region->region);
            $this->assertEquals(self::REGION_CODE, $region->regionCode);
            $this->assertEquals(self::REGION_ID, $region->regionId);
            $this->assertEquals($expected[intval($key)], $address->street);
        }
    }

}

require_once realpath(__DIR__ . '/../_files/test_interfaces.php');

