<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_TaxClass_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createDataProvider
     *
     * @param string $classType
     * @param string $className
     * @param PHPUnit_Framework_MockObject_MockObject $classTypeMock
     */
    public function testCreate($classType, $className, $classTypeMock)
    {
        $classMock = $this->getMock('Magento_Tax_Model_Class', array('getClassType', 'getId'), array(), '', false);
        $classMock->expects($this->once())->method('getClassType')->will($this->returnValue($classType));
        $classMock->expects($this->once())->method('getId')->will($this->returnValue(1));

        $objectManager = $this->getMock('Magento\ObjectManager', array(), array('create'), '', false);
        $objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo($className), $this->equalTo(array('data' => array('id' => 1))))
            ->will($this->returnValue($classTypeMock));

        $taxClassFactory = new Magento_Tax_Model_TaxClass_Factory($objectManager);
        $this->assertEquals($classTypeMock, $taxClassFactory->create($classMock));
    }

    public function createDataProvider()
    {
        $customerClassMock = $this->getMock('Magento_Tax_Model_TaxClass_Type_Customer', array(), array(), '', false);
        $productClassMock = $this->getMock('Magento_Tax_Model_TaxClass_Type_Product', array(), array(), '', false);
        return array(
            array(
                Magento_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER,
                'Magento_Tax_Model_TaxClass_Type_Customer',
                $customerClassMock
            ),
            array(
                Magento_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT,
                'Magento_Tax_Model_TaxClass_Type_Product',
                $productClassMock
            ),
        );
    }

    public function testCreateWithWrongClassType()
    {
        $wrongClassType = 'TYPE';
        $classMock = $this->getMock('Magento_Tax_Model_Class', array('getClassType', 'getId'), array(), '', false);
        $classMock->expects($this->once())->method('getClassType')->will($this->returnValue($wrongClassType));

        $objectManager = $this->getMock('Magento\ObjectManager', array(), array(), '', false);

        $taxClassFactory = new Magento_Tax_Model_TaxClass_Factory($objectManager);

        $this->setExpectedException(
            'Magento_Core_Exception',
            sprintf('Invalid type of tax class "%s"', $wrongClassType)
        );
        $taxClassFactory->create($classMock);
    }
}
