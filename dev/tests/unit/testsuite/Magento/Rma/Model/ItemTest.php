<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Model;

/**
 * Class ItemTest
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Model\Item
     */
    protected $model;

    /**
     * @var \Magento\Rma\Model\Resource\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Rma\Model\RmaFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaFactoryMock;

    /**
     * @var \Magento\Rma\Model\Rma|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaMock;
    /**
     * Test setUp
     */
    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->resourceMock = $this->getMock(
            'Magento\Rma\Model\Resource\Item',
            [],
            [],
            '',
            false
        );
        $this->rmaFactoryMock = $this->getMock(
            'Magento\Rma\Model\RmaFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->rmaMock = $this->getMock(
            'Magento\Rma\Model\Rma',
            ['getOrderId', '__wakeup', 'load'],
            [],
            '',
            false
        );
        $this->model = $objectManager->getObject(
            'Magento\Rma\Model\Item', [
                'resource' => $this->resourceMock,
                'rmaFactory' => $this->rmaFactoryMock,
                'data' => [
                    'order_item_id' => 3,
                    'rma_entity_id' => 4,
                ]
            ]
        );
    }

    /**
     * test getReturnableQty
     */
    public function testGetReturnableQty()
    {
        $this->rmaFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->rmaMock));
        $this->rmaMock->expects($this->once())
            ->method('load')
            ->with($this->equalTo(4))
            ->will($this->returnSelf());
        $this->rmaMock->expects($this->once())
            ->method('getOrderId')
            ->will($this->returnValue(3));
        $this->resourceMock->expects($this->once())
            ->method('getReturnableItems')
            ->with($this->equalTo(3))
            ->will($this->returnValue([3 => 100.50, 4 => 50.00]));
        $this->assertEquals(100.50, $this->model->getReturnableQty());
    }
}
