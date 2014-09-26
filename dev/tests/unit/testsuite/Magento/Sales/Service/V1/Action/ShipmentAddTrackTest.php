<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class ShipmentAddTrackTest
 */
class ShipmentAddTrackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\ShipmentAddTrack
     */
    protected $shipmentAddTrack;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\TrackConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $trackConverterMock;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\Track|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataModelMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\ShipmentTrack|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataObjectMock;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->trackConverterMock = $this->getMock(
            'Magento\Sales\Model\Order\Shipment\TrackConverter',
            ['getModel'],
            [],
            '',
            false
        );
        $this->dataModelMock = $this->getMock(
            'Magento\Sales\Model\Order\Shipment\Track',
            ['save', '__wakeup'],
            [],
            '',
            false
        );
        $this->dataObjectMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\ShipmentTrack',
            [],
            [],
            '',
            false
        );
        $this->shipmentAddTrack = new ShipmentAddTrack($this->trackConverterMock);
    }

    /**
     * Test shipment add track service
     */
    public function testInvoke()
    {
        $this->trackConverterMock->expects($this->once())
            ->method('getModel')
            ->with($this->equalTo($this->dataObjectMock))
            ->will($this->returnValue($this->dataModelMock));
        $this->dataModelMock->expects($this->once())
            ->method('save');
        $this->assertTrue($this->shipmentAddTrack->invoke($this->dataObjectMock));
    }
}
