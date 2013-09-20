<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_SalesArchive_Model_ArchivalListTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $_model Magento_SalesArchive_Model_ArchivalList
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento_ObjectManager_ObjectManager',
            array('get', 'create'), array(), '', false);

        $this->_model = new Magento_SalesArchive_Model_ArchivalList($this->_objectManagerMock);
    }

    /**
     * @dataProvider dataProviderGetResourcePositive
     * @param string $entity
     * @param string $className
     */
    public function testGetResourcePositive($entity, $className)
    {
        $this->_objectManagerMock->expects($this->once())
            ->method('get')
            ->will($this->returnArgument(0));
        $this->assertEquals($className, $this->_model->getResource($entity));
    }

    public function dataProviderGetResourcePositive()
    {
        return array(
            array('order', 'Magento_Sales_Model_Resource_Order'),
            array('invoice', 'Magento_Sales_Model_Resource_Order_Invoice'),
            array('shipment', 'Magento_Sales_Model_Resource_Order_Shipment'),
            array('creditmemo', 'Magento_Sales_Model_Resource_Order_Creditmemo'),
        );
    }

    public function testGetResourceNegative()
    {
        $this->setExpectedException('LogicException', 'FAKE!ENTITY entity isn\'t allowed');
        $this->_model->getResource('FAKE!ENTITY');
    }

    /**
     * @dataProvider dataGetEntityByObject
     * @param string|bool $entity
     * @param string $className
     */
    public function testGetEntityByObject($entity, $className)
    {
        $object = $this->getMock($className, array(), array(), '', false);
        $this->assertEquals($entity, $this->_model->getEntityByObject($object));
    }

    public function dataGetEntityByObject()
    {
        return array(
            array('order', 'Magento_Sales_Model_Resource_Order'),
            array('invoice', 'Magento_Sales_Model_Resource_Order_Invoice'),
            array('shipment', 'Magento_Sales_Model_Resource_Order_Shipment'),
            array('creditmemo', 'Magento_Sales_Model_Resource_Order_Creditmemo'),
            array(false, 'Magento_Object'),
        );
    }
}
