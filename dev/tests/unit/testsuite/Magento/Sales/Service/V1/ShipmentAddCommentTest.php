<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Class ShipmentAddCommentTest
 */
class ShipmentAddCommentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\ShipmentAddComment
     */
    protected $shipmentAddComment;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\CommentConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $commentConverterMock;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\Comment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataModelMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\Shipment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataObjectMock;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->commentConverterMock = $this->getMock(
            'Magento\Sales\Model\Order\Shipment\CommentConverter',
            ['getModel'],
            [],
            '',
            false
        );
        $this->dataModelMock = $this->getMock(
            'Magento\Sales\Model\Order\Shipment\Comment',
            ['save', '__wakeup'],
            [],
            '',
            false
        );
        $this->dataObjectMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\Comment',
            [],
            [],
            '',
            false
        );
        $this->shipmentAddComment = new \Magento\Sales\Service\V1\ShipmentAddComment($this->commentConverterMock);
    }

    /**
     * Test shipment add comment service
     */
    public function testInvoke()
    {
        $this->commentConverterMock->expects($this->once())
            ->method('getModel')
            ->with($this->equalTo($this->dataObjectMock))
            ->will($this->returnValue($this->dataModelMock));
        $this->dataModelMock->expects($this->once())
            ->method('save');
        $this->assertTrue($this->shipmentAddComment->invoke($this->dataObjectMock));
    }
}
