<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_UnitPrice
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_UnitPrice_Model_Entity_Backend_Unitprice_UnitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Magento_Core_Exception
     */
    public function testValidateShouldThrowExceptionIfNoConversionRateIsDefinedAndUnitPriceUsed()
    {
        $unitPriceModel = $this->getMockBuilder('Saas_UnitPrice_Model_Unitprice')
            ->setMethods(array('getConversionRate'))
            ->getMock();

        $unitPriceModel->expects($this->once())
            ->method('getConversionRate')
            ->with($this->equalTo(1), $this->equalTo(2))
            ->will($this->throwException(new Magento_Core_Exception));

        $unitPriceHelper = $this->getMockBuilder('Saas_UnitPrice_Helper_Data')
            ->setMethods(array('__'))
            ->disableOriginalConstructor()
            ->getMock();

        $unitPriceHelper->expects($this->once())
            ->method('__')
            ->will($this->returnArgument(0));

        $unit = $this->getMockBuilder('Saas_UnitPrice_Model_Entity_Backend_Unitprice_Unit')
            ->setMethods(array('getUnitPriceInstance', 'getUnitPriceHelper'))
            ->getMock();

        $unit->expects($this->once())
            ->method('getUnitPriceInstance')
            ->will($this->returnValue($unitPriceModel));

        $unit->expects($this->once())
            ->method('getUnitPriceHelper')
            ->will($this->returnValue($unitPriceHelper));

        $varienObj = $this->_prepareVarienObject();

        $unit->validate($varienObj);
    }

    public function testValidateShouldReturnFalseIfValueEmptyButAttributeRequired()
    {
        $varienObj = $this->_prepareVarienObject();
        $unit = $this->_getUnitMockObject(true, true, true);

        $this->assertFalse($unit->validate($varienObj));
    }

    public function testValidateShouldReturnTrueIfAttributeUniqueAndValueUnique()
    {
        $varienObj = $this->_prepareVarienObject();
        $unit = $this->_getUnitMockObject(false, true, true);

        $this->assertTrue($unit->validate($varienObj));
    }

    /**
     * @expectedException Mage_Eav_Exception
     */
    public function testValidateShouldThrowExceptionIfAttributeUniqueButValueNotUnique()
    {
        $varienObj = $this->_prepareVarienObject();
        $unit = $this->_getUnitMockObject(true, true, false);
        $unit->validate($varienObj);
    }

    /**
     * @return Magento_Object
     */
    protected function _prepareVarienObject()
    {
        $varienObj = new Magento_Object;
        $varienObj->setUnitPriceUse(true);
        $varienObj->setUnitPriceUnit(1);
        $varienObj->setUnitPriceBaseUnit(2);
        return $varienObj;
    }

    /**
     * @param bool $required
     * @param bool $unique
     * @param bool $empty
     * @return Saas_UnitPrice_Model_Entity_Backend_Unitprice_Unit
     */
    protected function _getUnitMockObject($required, $unique, $empty)
    {
        $unitPriceModel = $this->getMockBuilder('Saas_UnitPrice_Model_Unitprice')
            ->setMethods(array('getConversionRate'))
            ->getMock();

        $unitPriceModel->expects($this->once())
            ->method('getConversionRate')
            ->with($this->equalTo(1), $this->equalTo(2))
            ->will($this->returnValue('rate'));

        $unit = $this->getMockBuilder('Saas_UnitPrice_Model_Entity_Backend_Unitprice_Unit')
            ->setMethods(array('getUnitPriceInstance', 'getAttribute'))
            ->getMock();

        $unit->expects($this->once())
            ->method('getUnitPriceInstance')
            ->will($this->returnValue($unitPriceModel));

        $entityAttrAbstract = $this->getMockBuilder('Mage_Eav_Model_Entity_Attribute_Abstract')
            ->setMethods(array('getAttributeCode', 'getIsRequired', 'isValueEmpty', 'getIsUnique','getEntity'))
            ->disableOriginalConstructor()
            ->getMock();

        $entityAttrAbstract->expects($this->any())
            ->method('getAttributeCode')
            ->will($this->returnValue('unit_price_use'));

        $entityAttrAbstract->expects($this->any())
            ->method('getIsRequired')
            ->will($this->returnValue($required));

        $entityAttrAbstract->expects($this->any())
            ->method('getIsUnique')
            ->will($this->returnValue($unique));

        $entityAttrAbstract->expects($this->any())
            ->method('isValueEmpty')
            ->will($this->returnValue($empty));

        $eavEntity = $this->getMockBuilder('Mage_Eav_Model_Entity_Abstract')
            ->setMethods(array('checkAttributeUniqueValue'))
            ->getMock();

        $eavEntity->expects($this->any())
            ->method('checkAttributeUniqueValue')
            ->with($this->equalTo($entityAttrAbstract), $this->anything())
            ->will($this->throwException(new Mage_Eav_Exception));

        $entityAttrAbstract->expects($this->any())
            ->method('getEntity')
            ->will($this->returnValue($eavEntity));

        $unit->expects($this->any())
            ->method('getAttribute')
            ->will($this->returnValue($entityAttrAbstract));

        return $unit;
    }
}
