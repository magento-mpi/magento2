<?php
/**
 * Unit test for converter \Magento\Customer\Model\Converter
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model;

use Magento\Customer\Service\V1\Data\Eav\AttributeMetadata;
use Magento\Customer\Service\V1\Data\CustomerBuilder;
use Magento\Customer\Service\V1\CustomerMetadataServiceInterface;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject | AttributeMetadata */
    private $_attributeMetadata;

    /** @var  \PHPUnit_Framework_MockObject_MockObject | CustomerMetadataServiceInterface */
    private $_metadataService;

    public function setUp() {
        $this->_metadataService = $this->getMockForAbstractClass(
            'Magento\Customer\Service\V1\CustomerMetadataServiceInterface', [], '', false
        );

        $this->_metadataService
            ->expects($this->any())
            ->method('getAttributeMetadata')->will($this->returnValue($this->_attributeMetadata));

        $this->_attributeMetadata = $this->getMock(
            'Magento\Customer\Service\V1\Data\Eav\AttributeMetadata',
            [],
            [],
            '',
            false
        );
    }

    public function testCreateCustomerFromModel()
    {
        $customerModelMock =
            $this->getMockBuilder('Magento\Customer\Model\Customer')
                ->disableOriginalConstructor()
                ->setMethods(
                    array(
                        'getId',
                        'getFirstname',
                        'getLastname',
                        'getEmail',
                        'getAttributes',
                        'getData',
                        '__wakeup',
                    )
                )
                ->getMock();

        $attributeModelMock =
            $this->getMockBuilder('\Magento\Customer\Model\Attribute')
                ->disableOriginalConstructor()
                ->getMock();

        $attributeModelMock
            ->expects($this->at(0))
            ->method('getAttributeCode')
            ->will($this->returnValue('attribute_code'));

        $attributeModelMock
            ->expects($this->at(1))
            ->method('getAttributeCode')
            ->will($this->returnValue('attribute_code2'));

        $attributeModelMock
            ->expects($this->at(2))
            ->method('getAttributeCode')
            ->will($this->returnValue('attribute_code3'));

        $this->_mockReturnValue(
            $customerModelMock,
            array(
                'getId' => 1,
                'getFirstname' => 'Tess',
                'getLastname' => 'Tester',
                'getEmail' => 'ttester@example.com',
                'getAttributes' => [$attributeModelMock, $attributeModelMock, $attributeModelMock],
            )
        );

        $map = [
            ['attribute_code', null, 'attributeValue'],
            ['attribute_code2', null, 'attributeValue2'],
            ['attribute_code3', null, null],
        ];
        $customerModelMock
            ->expects($this->any())
            ->method('getData')
            ->will($this->returnValueMap($map));

        $customerBuilder = new CustomerBuilder($this->_metadataService);
        $customerFactory = $this->getMockBuilder('Magento\Customer\Model\CustomerFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $converter = new Converter($customerBuilder, $customerFactory);
        $customerDto = $converter->createCustomerFromModel($customerModelMock);

        $customerBuilder = new CustomerBuilder($this->_metadataService);
        $customerData = [
            'firstname' => 'Tess',
            'email' => 'ttester@example.com',
            'lastname' => 'Tester',
            'id' => 1,
            'attribute_code' => 'attributeValue',
            'attribute_code2' => 'attributeValue2'
        ];
        // There will be no attribute_code3: it has a value of null, so the converter will drop it
        $customerBuilder->populateWithArray($customerData);
        $expectedCustomerDto = $customerBuilder->create();

        $this->assertEquals($expectedCustomerDto, $customerDto);
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param array $valueMap
     */
    private function _mockReturnValue(\PHPUnit_Framework_MockObject_MockObject $mock, $valueMap)
    {
        foreach ($valueMap as $method => $value) {
            $mock->expects($this->any())
                ->method($method)
                ->will($this->returnValue($value));
        }
    }
}
