<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tax
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Tax_Model_Class_FactoryTest extends PHPUnit_Framework_TestCase
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
        $classMock = $this->getMock('Mage_Tax_Model_Class', array('getClassType', 'getId'), array(), '', false);
        $classMock->expects($this->once())->method('getClassType')->will($this->returnValue($classType));
        $classMock->expects($this->once())->method('getId')->will($this->returnValue(1));

        $objectManager = $this->getMock('Magento_ObjectManager', array(), array('create'), '', false);
        $objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo($className), $this->equalTo(array('data' => array('id' => 1))))
            ->will($this->returnValue($classTypeMock));

        $taxClassFactory = new Mage_Tax_Model_Class_Factory($objectManager);
        $this->assertEquals($classTypeMock, $taxClassFactory->create($classMock));
    }

    public function createDataProvider()
    {
        $customerClassMock = $this->getMock('Mage_Tax_Model_Class_Type_Customer', array(), array(), '', false);
        $productClassMock = $this->getMock('Mage_Tax_Model_Class_Type_Product', array(), array(), '', false);
        return array(
            array(
                Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER,
                'Mage_Tax_Model_Class_Type_Customer',
                $customerClassMock
            ),
            array(
                Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT,
                'Mage_Tax_Model_Class_Type_Product',
                $productClassMock
            ),
        );
    }

    public function testCreateWithWrongClassType()
    {
        $wrongClassType = 'TYPE';
        $classMock = $this->getMock('Mage_Tax_Model_Class', array('getClassType', 'getId'), array(), '', false);
        $classMock->expects($this->once())->method('getClassType')->will($this->returnValue($wrongClassType));

        $objectManager = $this->getMock('Magento_ObjectManager', array(), array(), '', false);

        $taxClassFactory = new Mage_Tax_Model_Class_Factory($objectManager);

        $this->setExpectedException(
            'Magento_Core_Exception',
            sprintf('Invalid type of tax class "%s"', $wrongClassType)
        );
        $taxClassFactory->create($classMock);
    }
}
