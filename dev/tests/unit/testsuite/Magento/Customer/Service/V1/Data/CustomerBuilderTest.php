<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1\Data;

use Magento\Customer\Service\V1\CustomerMetadataService;
use Magento\Customer\Service\V1\Data\Eav\AttributeMetadataBuilder;
use Magento\Service\Entity\AbstractObject;
use Magento\Service\Entity\AbstractObjectBuilder;

class CustomerBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Customer\Service\V1\Data\CustomerBuilder|\PHPUnit_Framework_TestCase */
    protected $_customerBuilder;

    /** @var \Magento\TestFramework\Helper\ObjectManager */
    protected $_objectManager;

    /** @var \Magento\Customer\Service\V1\CustomerMetadataService */
    private $_customerMetadataService;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Customer\Service\V1\CustomerMetadataService $customerMetadataService */
        $this->_customerMetadataService = $this->getMockBuilder('Magento\Customer\Service\V1\CustomerMetadataService')
            ->setMethods(['getCustomCustomerAttributeMetadata'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->_customerMetadataService->expects($this->any())
            ->method('getCustomCustomerAttributeMetadata')
            ->will($this->returnValue([
                        new \Magento\Object(['attribute_code' => 'warehouse_zip']),
                        new \Magento\Object(['attribute_code' => 'warehouse_alternate'])
                    ]));
        $this->_customerBuilder = new CustomerBuilder($this->_customerMetadataService);
        parent::setUp();
    }

    public function testMergeDataObjects()
    {
        $firstname1 = 'Firstname1';
        $lastnam1 = 'Lastname1';
        $email1 = 'email1@example.com';
        $firstDataObject = $this->_customerBuilder
            ->setFirstname($firstname1)
            ->setLastname($lastnam1)
            ->setEmail($email1)
            ->create();

        $lastname2 = 'Lastname2';
        $middlename2 = 'Middlename2';
        $secondDataObject = $this->_customerBuilder
            ->setLastname($lastname2)
            ->setMiddlename($middlename2)
            ->create();

        $mergedDataObject = $this->_customerBuilder->mergeDataObjects($firstDataObject, $secondDataObject);
        $this->assertNotSame($firstDataObject, $mergedDataObject,
            'A new object must be created for merged Data Object.'
        );
        $this->assertNotSame($secondDataObject, $mergedDataObject,
            'A new object must be created for merged Data Object.'
        );
        $expectedDataObject = [
            'firstname' => $firstname1,
            'lastname' => $lastname2,
            'middlename' => $middlename2,
            'email' => $email1
        ];
        $this->assertEquals($expectedDataObject, $mergedDataObject->__toArray(),
            'Data Objects were merged incorrectly.');
    }

    public function testMergeDataObjectsWitArray()
    {
        $firstname1 = 'Firstname1';
        $lastnam1 = 'Lastname1';
        $email1 = 'email1@example.com';
        $firstDataObject = $this->_customerBuilder
            ->setFirstname($firstname1)
            ->setLastname($lastnam1)
            ->setEmail($email1)
            ->create();

        $lastname2 = 'Lastname2';
        $middlename2 = 'Middlename2';
        $dataForMerge = ['lastname' => $lastname2, 'middlename' => $middlename2];

        $mergedDataObject = $this->_customerBuilder->mergeDataObjectWithArray($firstDataObject, $dataForMerge);
        $this->assertNotSame($firstDataObject, $mergedDataObject,
            'A new object must be created for merged Data Object.'
        );
        $expectedDataObject = [
            'firstname' => $firstname1,
            'lastname' => $lastname2,
            'middlename' => $middlename2,
            'email' => $email1
        ];
        $this->assertEquals($expectedDataObject, $mergedDataObject->__toArray(),
            'Data Object with array were merged incorrectly.'
        );
    }

    // @codingStandardsIgnoreStart
    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Wrong prototype object given. It can only be of
     * "Magento\Customer\Service\V1\Data\Customer" type.
     */
    // @codingStandardsIgnoreEnd
    public function testPopulateException()
    {
        $addressData = (new AddressBuilder(new RegionBuilder(), $this->_customerMetadataService))->create();
        $this->_customerBuilder->populate($addressData);
    }

    public function testPopulate()
    {
        $email = 'test@example.com';
        $customerBuilder1 = new CustomerBuilder($this->_customerMetadataService);
        $customerBuilder2 = new CustomerBuilder($this->_customerMetadataService);
        $customer = $customerBuilder1->setEmail($email)->create();
        $customerBuilder2
            ->setFirstname('fname')
            ->setLastname('lname')
            ->create();
        //Make sure email is not populated as yet
        $this->assertEquals(null, $customerBuilder2->create()->getEmail());
        $customerBuilder2->populate($customer);
        //Verify if email is set correctly
        $this->assertEquals($email, $customerBuilder2->create()->getEmail());
    }

    public function testPopulateWithArray()
    {
        $customerData = [
            'email' => 'test@example.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'unknown_key' => 'Golden Necklace'
        ];
        $customer = $this->_customerBuilder->populateWithArray($customerData)->create();
        $expectedData = $addressData = [
            'email' => 'test@example.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
        ];
        $this->assertEquals($expectedData, $customer->__toArray());
    }

    public function testPopulateWithArrayCustomAttributes()
    {
        $customerData = [
            'email' => 'test@example.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'unknown_key' => 'Golden Necklace',
            'warehouse_zip' => '78777',
            'warehouse_alternate' => '90051'
        ];
        $customer = $this->_customerBuilder->populateWithArray($customerData)->create();

        $expectedData = [
            'email' => 'test@example.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
            Customer::CUSTOM_ATTRIBUTES_KEY => [
                'warehouse_zip' => '78777',
                'warehouse_alternate' => '90051'
            ]
        ];
        $this->assertEquals($expectedData, $customer->__toArray());
    }

    public function testSetCustomAttribute()
    {
        $customer = $this->_customerBuilder->setCustomAttribute('warehouse_zip', '78777')
            ->setCustomAttribute('warehouse_alternate', '90051')
            ->create();
        $this->assertEquals('78777', $customer->getCustomAttribute('warehouse_zip'));
        $this->assertEquals('90051', $customer->getCustomAttribute('warehouse_alternate'));

        $customAttributes = [Customer::CUSTOM_ATTRIBUTES_KEY => [
            'warehouse_zip' => '78777',
            'warehouse_alternate' => '90051'
        ]];
        $this->assertEquals($customAttributes[Customer::CUSTOM_ATTRIBUTES_KEY], $customer->getCustomAttributes());
        $this->assertEquals($customAttributes, $customer->__toArray());
    }

    public function testSetCustomAttributes()
    {
        $customerData = [
            'email' => 'test@example.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'unknown_key' => 'Golden Necklace',
            'warehouse_zip' => '78777',
            'warehouse_alternate' => '90051'
        ];
        $expectedData = [
            'email' => 'test@example.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
            Customer::CUSTOM_ATTRIBUTES_KEY => [
                'warehouse_zip' => '78777',
                'warehouse_alternate' => '90051'
            ]
        ];
        $customer = $this->_customerBuilder->setCustomAttributes($customerData)
            ->create();

        $this->assertEquals('78777', $customer->getCustomAttribute('warehouse_zip'));
        $this->assertEquals('90051', $customer->getCustomAttribute('warehouse_alternate'));
        $this->assertEquals($expectedData[Customer::CUSTOM_ATTRIBUTES_KEY], $customer->getCustomAttributes());
        $this->assertEquals($expectedData, $customer->__toArray());
    }
}
