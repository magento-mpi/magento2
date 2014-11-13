<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api;

use Magento\Customer\Api\Data\CustomerInterface;

/**
 * Class implements tests for SimpleDataObjectConverter class.
 */
class SimpleDataObjectConverterTest extends \PHPUnit_Framework_TestCase
{
    /** @var SimpleDataObjectConverter */
    protected $dataObjectConverter;

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
     * Expected street in customer addresses
     *
     * @var array
     */
    private $expectedStreet = [['Green str, 67'], ['Black str, 48', 'Building D']];

    /**
     * Set up helper.
     */
    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->dataObjectConverter = $objectManager->getObject('Magento\Framework\Api\SimpleDataObjectConverter');
        parent::setUp();
    }

    public function testToFlatArray()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $typeProcessor = $objectManager->getObject('Magento\Framework\Reflection\TypeProcessor');
        $dataObjectProcessor = $objectManager->getObject(
            'Magento\Framework\Reflection\DataObjectProcessor',
            ['typeProcessor' => $typeProcessor]
        );
        $extensibleDataObjectConverter = $objectManager->getObject(
            'Magento\Framework\Api\ExtensibleDataObjectConverter',
            ['dataObjectProcessor' => $dataObjectProcessor]
        );
        //Unpack Data Object as an array and convert keys to camelCase to match property names in WSDL
        $response = $extensibleDataObjectConverter->toFlatArray($this->getCustomerDetails());
        //Check if keys are correctly converted to camel case wherever necessary
        $this->assertEquals(self::FIRSTNAME, $response['firstname']);
        $this->assertEquals(self::GROUP_ID, $response['group_id']);
        $this->assertEquals(self::REGION, $response['region']);
        $this->assertEquals(self::REGION_CODE, $response['region_code']);
        $this->assertEquals(self::REGION_ID, $response['region_id']);
        //TODO : FIX toFlatArray since it has issues in converting Street array correctly as it overwrites the data.
    }

    public function testConvertKeysToCamelCase()
    {
        //Unpack as an array and convert keys to camelCase to match property names in WSDL
        $response = $this->dataObjectConverter->convertKeysToCamelCase($this->getCustomerDetails()->__toArray());
        //Check if keys are correctly converted to camel case wherever necessary
        $this->assertEquals(self::FIRSTNAME, $response['customer']['firstname']);
        $this->assertEquals(self::GROUP_ID, $response['customer']['groupId']);
        foreach ($response['addresses'] as $key => $address) {
            $region = $address['region'];
            $this->assertEquals(self::REGION, $region['region']);
            $this->assertEquals(self::REGION_CODE, $region['regionCode']);
            $this->assertEquals(self::REGION_ID, $region['regionId']);
            $this->assertEquals($this->expectedStreet[$key], $address['street']);
        }
    }

    public function testConvertSoapStdObjectToArray()
    {
        $stdObject = json_decode(json_encode($this->getCustomerDetails()->__toArray()), false);
        $addresses = $stdObject->addresses;
        unset($stdObject->addresses);
        $stdObject->addresses = new \stdClass();
        $stdObject->addresses->item = $addresses;
        $response = $this->dataObjectConverter->convertStdObjectToArray($stdObject);

        //Check array conversion
        $this->assertTrue(is_array($response['customer']));
        $this->assertTrue(is_array($response['addresses']));
        $this->assertEquals(2, count($response['addresses']['item']));

        //Check if data is correct
        $this->assertEquals(self::FIRSTNAME, $response['customer']['firstname']);
        $this->assertEquals(self::GROUP_ID, $response['customer']['group_id']);
        foreach ($response['addresses']['item'] as $key => $address) {
            $region = $address['region'];
            $this->assertEquals(self::REGION, $region['region']);
            $this->assertEquals(self::REGION_CODE, $region['region_code']);
            $this->assertEquals(self::REGION_ID, $region['region_id']);
            $this->assertEquals($this->expectedStreet[$key], $address['street']);
        }
    }

    /**
     * Get a sample Customer details data object
     *
     * @return \Magento\Customer\Service\V1\Data\CustomerDetails
     */
    private function getCustomerDetails()
    {
        $this->markTestSkipped("Will delete eventually");
        $objectManager =  new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Customer\Service\V1\Data\AddressBuilder $addressBuilder */
        $addressBuilder = $objectManager->getObject('Magento\Customer\Service\V1\Data\AddressBuilder');
        /** @var \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $metadataService */
        $metadataService = $this->getMockBuilder('Magento\Customer\Service\V1\CustomerMetadataService')
            ->disableOriginalConstructor()
            ->getMock();
        $metadataService->expects($this->any())
            ->method('getCustomAttributesMetadata')
            ->will($this->returnValue([]));
        /** @var \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder */
        $customerBuilder = $objectManager->getObject(
            'Magento\Customer\Api\Data\CustomerDataBuilder',
            ['metadataService' => $metadataService]
        );
        /** @var \Magento\Customer\Service\V1\Data\CustomerDetailsBuilder $customerDetailsBuilder */
        $customerDetailsBuilder =
            $objectManager->getObject('Magento\Customer\Service\V1\Data\CustomerDetailsBuilder');

        $street1 = ['Green str, 67'];
        $street2 = ['Black str, 48', 'Building D'];
        $addressBuilder->setId(1)
            ->setCountryId('US')
            ->setCustomerId(1)
            ->setDefaultBilling(true)
            ->setDefaultShipping(true)
            ->setPostcode('75477')
            ->setRegion(
                $objectManager->getObject('\Magento\Customer\Service\V1\Data\RegionBuilder')
                    ->setRegionCode(self::REGION_CODE)
                    ->setRegion(self::REGION)
                    ->setRegionId(self::REGION_ID)
                    ->create()
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
                $objectManager->getObject('\Magento\Customer\Service\V1\Data\RegionBuilder')
                    ->setRegionCode(self::REGION_CODE)
                    ->setRegion(self::REGION)
                    ->setRegionId(self::REGION_ID)
                    ->create()
            )
            ->setStreet($street2)
            ->setCity('CityX')
            ->setTelephone('3234676')
            ->setFirstname('John')
            ->setLastname('Smith');
        $address2 = $addressBuilder->create();

        $customerData = [
            CustomerInterface::FIRSTNAME => self::FIRSTNAME,
            CustomerInterface::LASTNAME => self::LASTNAME,
            CustomerInterface::EMAIL => 'janedoe@example.com',
            CustomerInterface::CONFIRMATION => self::CONFIRMATION,
            CustomerInterface::CREATED_AT => self::CREATED_AT,
            CustomerInterface::CREATED_IN => self::STORE_NAME,
            CustomerInterface::DOB => self::DOB,
            CustomerInterface::GENDER => self::GENDER,
            CustomerInterface::GROUP_ID => self::GROUP_ID,
            CustomerInterface::MIDDLENAME => self::MIDDLENAME,
            CustomerInterface::PREFIX => self::PREFIX,
            CustomerInterface::STORE_ID => self::STORE_ID,
            CustomerInterface::SUFFIX => self::SUFFIX,
            CustomerInterface::TAXVAT => self::TAXVAT,
            CustomerInterface::WEBSITE_ID => self::WEBSITE_ID
        ];
        $customerData = $customerBuilder->populateWithArray($customerData)->create();
        $customerDetails = $customerDetailsBuilder->setAddresses([$address, $address2])
            ->setCustomer($customerData)
            ->create();

        return $customerDetails;
    }
}
