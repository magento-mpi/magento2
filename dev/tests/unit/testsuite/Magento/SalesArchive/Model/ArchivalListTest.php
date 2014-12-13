<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        $this->_objectManagerMock = $this->getMock(
            'Magento\Framework\ObjectManager\ObjectManager',
            ['get', 'create'],
            [],
            '',
            false
        );

        $this->_model = new \Magento\SalesArchive\Model\ArchivalList($this->_objectManagerMock);
    }

    /**
     * @dataProvider dataProviderGetResourcePositive
     * @param string $entity
     * @param string $className
     */
    public function testGetResourcePositive($entity, $className)
    {
        $this->_objectManagerMock->expects($this->once())->method('get')->will($this->returnArgument(0));
        $this->assertEquals($className, $this->_model->getResource($entity));
    }

    public function dataProviderGetResourcePositive()
    {
        return [
            ['order', 'Magento\Sales\Model\Resource\Order'],
            ['invoice', 'Magento\Sales\Model\Resource\Order\Invoice'],
            ['shipment', 'Magento\Sales\Model\Resource\Order\Shipment'],
            ['creditmemo', 'Magento\Sales\Model\Resource\Order\Creditmemo']
        ];
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
        $object = $this->getMock($className, [], [], '', false);
        $this->assertEquals($entity, $this->_model->getEntityByObject($object));
    }

    public function dataGetEntityByObject()
    {
        return [
            ['order', 'Magento\Sales\Model\Resource\Order'],
            ['invoice', 'Magento\Sales\Model\Resource\Order\Invoice'],
            ['shipment', 'Magento\Sales\Model\Resource\Order\Shipment'],
            ['creditmemo', 'Magento\Sales\Model\Resource\Order\Creditmemo'],
            [false, 'Magento\Framework\Object']
        ];
    }
}
