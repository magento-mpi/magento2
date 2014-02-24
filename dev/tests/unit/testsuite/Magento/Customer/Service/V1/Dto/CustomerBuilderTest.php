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
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_customerBuilder = $objectManager->getObject('Magento\Customer\Service\V1\Data\CustomerBuilder');
        parent::setUp();
    }

    public function testMergeDtos()
    {
        $firstname1 = 'Firstname1';
        $lastnam1 = 'Lastname1';
        $email1 = 'email1@example.com';
        $firstDto = $this->_customerBuilder
            ->setFirstname($firstname1)
            ->setLastname($lastnam1)
            ->setEmail($email1)
            ->create();

        $lastname2 = 'Lastname2';
        $middlename2 = 'Middlename2';
        $secondDto = $this->_customerBuilder
            ->setLastname($lastname2)
            ->setMiddlename($middlename2)
            ->create();

        $mergedDto = $this->_customerBuilder->mergeDtos($firstDto, $secondDto);
        $this->assertNotSame($firstDto, $mergedDto, 'A new object must be created for merged DTO.');
        $this->assertNotSame($secondDto, $mergedDto, 'A new object must be created for merged DTO.');
        $expectedDtoData = [
            'firstname' => $firstname1,
            'lastname' => $lastname2,
            'middlename' => $middlename2,
            'email' => $email1
        ];
        $this->assertEquals($expectedDtoData, $mergedDto->__toArray(), 'DTOs were merged incorrectly.');
    }

    public function testMergeDtoWitArray()
    {
        $firstname1 = 'Firstname1';
        $lastnam1 = 'Lastname1';
        $email1 = 'email1@example.com';
        $firstDto = $this->_customerBuilder
            ->setFirstname($firstname1)
            ->setLastname($lastnam1)
            ->setEmail($email1)
            ->create();

        $lastname2 = 'Lastname2';
        $middlename2 = 'Middlename2';
        $dataForMerge = ['lastname' => $lastname2, 'middlename' => $middlename2];

        $mergedDto = $this->_customerBuilder->mergeDtoWithArray($firstDto, $dataForMerge);
        $this->assertNotSame($firstDto, $mergedDto, 'A new object must be created for merged DTO.');
        $expectedDtoData = [
            'firstname' => $firstname1,
            'lastname' => $lastname2,
            'middlename' => $middlename2,
            'email' => $email1
        ];
        $this->assertEquals($expectedDtoData, $mergedDto->__toArray(), 'DTO with array were merged incorrectly.');
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Wrong prototype object given. It can only be of "Magento\Customer\Service\V1\Dto\Customer" type.
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