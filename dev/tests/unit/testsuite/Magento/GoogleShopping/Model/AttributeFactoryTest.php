<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GoogleShopping_Model_AttributeFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Get object manager mock
     *
     * @return Magento_ObjectManager
     */
    protected function _createObjectManager()
    {
        return $this->getMockBuilder('Magento_ObjectManager')
            ->setMethods(array('create'))
            ->getMockForAbstractClass();
    }

    /**
     * Get helper mock
     *
     * @return Magento_GoogleShopping_Helper_Data
     */
    protected function _createGsData()
    {
        return $this->getMockBuilder('Magento_GoogleShopping_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
    }

    /**
     * Get default attribute mock
     *
     * @return Magento_GoogleShopping_Model_Attribute_Default
     */
    protected function _createDefaultAttribute()
    {
        return $this->getMockBuilder('Magento_GoogleShopping_Model_Attribute_Default')
            ->disableOriginalConstructor()
            ->setMethods(null)
            ->getMock();
    }

    /**
     * @param string $name
     * @param string $expected
     * @dataProvider createAttributeDataProvider
     */
    public function testCreateAttribute($name, $expected)
    {
        $objectManager = $this->_createObjectManager();
        $objectManager->expects($this->once())
            ->method('create')
            ->with($this->equalTo('Magento_GoogleShopping_Model_Attribute_' . $expected))
            ->will($this->returnValue($this->_createDefaultAttribute()));
        $attributeFactory = new Magento_GoogleShopping_Model_AttributeFactory($objectManager, $this->_createGsData());
        $attribute = $attributeFactory->createAttribute($name);
        $this->assertEquals($name, $attribute->getName());
    }

    public function createAttributeDataProvider()
    {
        return array(
            array('name', 'Name'),
            array('first_second', 'First_Second'),
            array('first_second_third', 'First_Second_Third')
        );
    }

    /**
     * @param bool $throwException
     * @dataProvider createAttributeDefaultDataProvider
     */
    public function testCreateAttributeDefault($throwException)
    {
        $objectManager = $this->_createObjectManager();
        $objectManager->expects($this->at(0))
            ->method('create')
            ->with($this->equalTo('Magento_GoogleShopping_Model_Attribute_Name'))
            ->will($throwException ? $this->throwException(new Exception()) : $this->returnValue(false));
        $objectManager->expects($this->at(1))
            ->method('create')
            ->with($this->equalTo('Magento_GoogleShopping_Model_Attribute_Default'))
            ->will($this->returnValue($this->_createDefaultAttribute()));
        $attributeFactory = new Magento_GoogleShopping_Model_AttributeFactory($objectManager, $this->_createGsData());
        $attribute = $attributeFactory->createAttribute('name');
        $this->assertEquals('name', $attribute->getName());
    }

    public function createAttributeDefaultDataProvider()
    {
        return array(array(true), array(false));
    }

    public function testCreate()
    {
        $objectManager = $this->_createObjectManager();
        $objectManager->expects($this->once())
            ->method('create')
            ->with('Magento_GoogleShopping_Model_Attribute')
            ->will($this->returnValue('some value'));
        $attributeFactory = new Magento_GoogleShopping_Model_AttributeFactory($objectManager, $this->_createGsData());
        $attribute = $attributeFactory->create();
        $this->assertEquals('some value', $attribute);
    }
}
