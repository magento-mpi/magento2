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

class ConverterTest extends \PHPUnit_Framework_TestCase
{
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

        $customerBuilder = new \Magento\Customer\Service\V1\Dto\CustomerBuilder();
        $customerFactory = $this->getMockBuilder('Magento\Customer\Model\CustomerFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $converter = new Converter($customerBuilder, $customerFactory);
        $customerDto = $converter->createCustomerFromModel($customerModelMock);

        $customerBuilder = new \Magento\Customer\Service\V1\Dto\CustomerBuilder();
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
     * @dataProvider createCustomerFromModelBadParamDataProvider
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage customer model is invalid
     */
    public function testCreateCustomerFromModelBadParam($param)
    {
        $customerBuilder = $this->getMockBuilder('Magento\Customer\Service\V1\Dto\CustomerBuilder')
            ->disableOriginalConstructor()
            ->getMock();

        $customerFactory = $this->getMockBuilder('Magento\Customer\Model\CustomerFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $converter = new Converter($customerBuilder, $customerFactory);
        $converter->createCustomerFromModel($param);
    }

    public function createCustomerFromModelBadParamDataProvider()
    {
        return [
            [null],
            ['a string'],
            [5],
            [new \Magento\Object()],
        ];
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param array $valueMap
     */
    private function _mockReturnValue($mock, $valueMap)
    {
        foreach ($valueMap as $method => $value) {
            $mock->expects($this->any())
                ->method($method)
                ->will($this->returnValue($value));
        }
    }
}
