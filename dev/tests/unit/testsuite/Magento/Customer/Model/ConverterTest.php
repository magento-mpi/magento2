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


        $converter = new Converter();
        $customerDto = $converter->createCustomerFromModel($customerModelMock);
        $expectedCustomerDto = new \Magento\Customer\Service\Entity\V1\Customer();
        $expectedCustomerDto->setFirstname('Tess');
        $expectedCustomerDto->setEmail('ttester@example.com');
        $expectedCustomerDto->setLastname('Tester');
        $expectedCustomerDto->setCustomerId(1);
        $expectedCustomerDto->setAttribute('attribute_code', 'attributeValue');
        $expectedCustomerDto->setAttribute('attribute_code2', 'attributeValue2');
        // There will be no attribute_code3: it has a value of null, so the converter will drop it

        $this->assertEquals($expectedCustomerDto, $customerDto);
    }

    /**
     * @dataProvider createCustomerFromModelBadParamDataProvider
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage customer model is invalid
     */
    public function testCreateCustomerFromModelBadParam($param)
    {
        $converter = new Converter();
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
