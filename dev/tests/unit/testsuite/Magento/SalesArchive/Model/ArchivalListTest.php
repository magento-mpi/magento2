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

namespace Magento\SalesArchive\Model;

class ArchivalListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $_model \Magento\SalesArchive\Model\ArchivalList
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager\ObjectManager',
            array('get', 'create'), array(), '', false);

        $this->_model = new \Magento\SalesArchive\Model\ArchivalList($this->_objectManagerMock);
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
            array('order', 'Magento\Sales\Model\Resource\Order'),
            array('invoice', 'Magento\Sales\Model\Resource\Order\Invoice'),
            array('shipment', 'Magento\Sales\Model\Resource\Order\Shipment'),
            array('creditmemo', 'Magento\Sales\Model\Resource\Order\Creditmemo'),
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
            array('order', 'Magento\Sales\Model\Resource\Order'),
            array('invoice', 'Magento\Sales\Model\Resource\Order\Invoice'),
            array('shipment', 'Magento\Sales\Model\Resource\Order\Shipment'),
            array('creditmemo', 'Magento\Sales\Model\Resource\Order\Creditmemo'),
            array(false, 'Magento\Object'),
        );
    }
}
