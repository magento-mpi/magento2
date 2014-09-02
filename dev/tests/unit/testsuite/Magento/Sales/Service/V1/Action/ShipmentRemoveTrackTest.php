<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class ShipmentRemoveTrackTest
 */
class ShipmentRemoveTrackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\ShipmentRemoveTrack
     */
    protected $shipmentRemoveTrack;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\TrackRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $trackRepositoryMock;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\Track|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $trackMock;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->trackRepositoryMock = $this->getMock(
            '\Magento\Sales\Model\Order\Shipment\TrackRepository',
            ['get'],
            [],
            '',
            false
        );
        $this->trackMock = $this->getMock(
            'Magento\Sales\Model\Order\Shipment\Track',
            ['delete', '__wakeup'],
            [],
            '',
            false
        );

        $this->shipmentRemoveTrack = new ShipmentRemoveTrack($this->trackRepositoryMock);
    }

    /**
     * Test shipment remove track
     */
    public function testInvoke()
    {
        $this->trackRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->trackMock));
        $this->trackMock->expects($this->once())
            ->method('delete');

        $this->assertTrue($this->shipmentRemoveTrack->invoke(1));
    }
}
