<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tax\Model\TaxClass;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $classType
     * @param string $className
     * @param \PHPUnit_Framework_MockObject_MockObject $classTypeMock
     */
    public function testCreate($classType, $className, $classTypeMock)
    {
        $classMock = $this->getMock(
            'Magento\Tax\Model\ClassModel',
            ['getClassType', 'getId', '__wakeup'],
            [],
            '',
            false
        );
        $classMock->expects($this->once())->method('getClassType')->will($this->returnValue($classType));
        $classMock->expects($this->once())->method('getId')->will($this->returnValue(1));

        $objectManager = $this->getMock('Magento\Framework\ObjectManagerInterface');
        $objectManager->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            $this->equalTo($className),
            $this->equalTo(['data' => ['id' => 1]])
        )->will(
            $this->returnValue($classTypeMock)
        );

        $taxClassFactory = new \Magento\Tax\Model\TaxClass\Factory($objectManager);
        $this->assertEquals($classTypeMock, $taxClassFactory->create($classMock));
    }

    public function createDataProvider()
    {
        $customerClassMock = $this->getMock('Magento\Tax\Model\TaxClass\Type\Customer', [], [], '', false);
        $productClassMock = $this->getMock('Magento\Tax\Model\TaxClass\Type\Product', [], [], '', false);
        return [
            [
                \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER,
                'Magento\Tax\Model\TaxClass\Type\Customer',
                $customerClassMock,
            ],
            [
                \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT,
                'Magento\Tax\Model\TaxClass\Type\Product',
                $productClassMock
            ]
        ];
    }

    public function testCreateWithWrongClassType()
    {
        $wrongClassType = 'TYPE';
        $classMock = $this->getMock(
            'Magento\Tax\Model\ClassModel',
            ['getClassType', 'getId', '__wakeup'],
            [],
            '',
            false
        );
        $classMock->expects($this->once())->method('getClassType')->will($this->returnValue($wrongClassType));

        $objectManager = $this->getMock('Magento\Framework\ObjectManagerInterface');

        $taxClassFactory = new \Magento\Tax\Model\TaxClass\Factory($objectManager);

        $this->setExpectedException(
            'Magento\Framework\Model\Exception',
            sprintf('Invalid type of tax class "%s"', $wrongClassType)
        );
        $taxClassFactory->create($classMock);
    }
}
