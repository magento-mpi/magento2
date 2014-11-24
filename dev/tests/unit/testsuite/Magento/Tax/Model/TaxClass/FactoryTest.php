<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
            array('getClassType', 'getId', '__wakeup'),
            array(),
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
            $this->equalTo(array('data' => array('id' => 1)))
        )->will(
            $this->returnValue($classTypeMock)
        );

        $taxClassFactory = new \Magento\Tax\Model\TaxClass\Factory($objectManager);
        $this->assertEquals($classTypeMock, $taxClassFactory->create($classMock));
    }

    public function createDataProvider()
    {
        $customerClassMock = $this->getMock('Magento\Tax\Model\TaxClass\Type\Customer', array(), array(), '', false);
        $productClassMock = $this->getMock('Magento\Tax\Model\TaxClass\Type\Product', array(), array(), '', false);
        return array(
            array(
                \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER,
                'Magento\Tax\Model\TaxClass\Type\Customer',
                $customerClassMock
            ),
            array(
                \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT,
                'Magento\Tax\Model\TaxClass\Type\Product',
                $productClassMock
            )
        );
    }

    public function testCreateWithWrongClassType()
    {
        $wrongClassType = 'TYPE';
        $classMock = $this->getMock(
            'Magento\Tax\Model\ClassModel',
            array('getClassType', 'getId', '__wakeup'),
            array(),
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
