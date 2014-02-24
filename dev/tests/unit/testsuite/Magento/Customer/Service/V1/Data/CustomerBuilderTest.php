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
use Magento\Service\Entity\AbstractDto;
use Magento\Service\Entity\AbstractDtoBuilder;

class CustomerBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Customer\Service\V1\Data\CustomerBuilder */
    protected $_customerBuilder;

    /** @var \Magento\TestFramework\Helper\ObjectManager */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_customerBuilder = $this->_objectManager->getObject('Magento\Customer\Service\V1\Data\CustomerBuilder');
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
        $this->assertNotSame($firstDataObject, $mergedDataObject, 'A new object must be created for merged DTO.');
        $this->assertNotSame($secondDataObject, $mergedDataObject, 'A new object must be created for merged DTO.');
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

        $mergedDataObject = $this->_customerBuilder->mergeDtoWithArray($firstDataObject, $dataForMerge);
        $this->assertNotSame($firstDataObject, $mergedDataObject, 'A new object must be created for merged DTO.');
        $expectedDataObject = [
            'firstname' => $firstname1,
            'lastname' => $lastname2,
            'middlename' => $middlename2,
            'email' => $email1
        ];
        $this->assertEquals($expectedDataObject, $mergedDataObject->__toArray(), 'DTO with array were merged incorrectly.');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Wrong prototype object given. It can only be of "Magento\Customer\Service\V1\Data\Customer" type.
     */
    public function testPopulateException()
    {
        $customerMetadataService = $this->_objectManager->getObject(
            'Magento\Customer\Service\V1\CustomerMetadataService'
        );
        $addressData = (new AddressBuilder(new RegionBuilder(), $customerMetadataService))->create();
        $this->_customerBuilder->populate($addressData);
    }

    public function testPopulate()
    {
        $email = 'test@example.com';
        $customerMetadataService = $this->_objectManager->getObject(
            'Magento\Customer\Service\V1\CustomerMetadataService'
        );
        $customerBuilder1 = (new CustomerBuilder($customerMetadataService));
        $customerBuilder2 = (new CustomerBuilder($customerMetadataService));
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
}
