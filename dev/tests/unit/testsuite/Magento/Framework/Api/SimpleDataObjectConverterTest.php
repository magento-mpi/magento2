<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Api;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Data\AbstractDataObject;

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
}
