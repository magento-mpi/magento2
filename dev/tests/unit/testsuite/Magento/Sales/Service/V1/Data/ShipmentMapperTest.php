<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

/**
 * Class ShipmentMapperTest
 */
class ShipmentMapperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ShipmentMapper
     */
    protected $shipmentMapper;

    /**
     * @var ShipmentBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentBuilderMock;

    /**
     * @var ShipmentItemMapper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentItemMapperMock;

    /**
     * @var ShipmentTrackMapper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentTrackMapperMock;

    /**
     * @var \Magento\Sales\Model\Order\Shipment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentMock;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentItemMock;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\Track|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentTrackMock;

    /**
     * SetUp
     *
     * @return void
     */
    protected function setUp()
    {
        $this->shipmentBuilderMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\ShipmentBuilder',
            ['populateWithArray', 'setItems', 'setTracks', 'create'],
            [],
            '',
            false
        );
        $this->shipmentItemMapperMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\ShipmentItemMapper',
            ['extractDto'],
            [],
            '',
            false
        );
        $this->shipmentTrackMapperMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\ShipmentTrackMapper',
            ['extractDto'],
            [],
            '',
            false
        );
        $this->shipmentMock = $this->getMock(
            'Magento\Sales\Model\Order\Shipment',
            ['getItemsCollection', 'getTracksCollection', 'getData', '__wakeup'],
            [],
            '',
            false
        );
        $this->shipmentItemMock = $this->getMock(
            'Magento\Sales\Model\Order\Shipment\Item',
            [],
            [],
            '',
            false
        );
        $this->shipmentMapper = new ShipmentMapper(
            $this->shipmentBuilderMock,
            $this->shipmentItemMapperMock,
            $this->shipmentTrackMapperMock
        );
    }

    /**
     * Run shipment mapper test
     *
     * @return void
     */
    public function testInvoke()
    {
        $this->shipmentMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(['field-1' => 'value-1']));
        $this->shipmentBuilderMock->expects($this->once())
            ->method('populateWithArray')
            ->with($this->equalTo(['field-1' => 'value-1']))
            ->will($this->returnSelf());

        $this->shipmentMock->expects($this->once())
            ->method('getItemsCollection')
            ->will($this->returnValue([$this->shipmentItemMock]));
        $this->shipmentItemMapperMock->expects($this->once())
            ->method('extractDto')
            ->with($this->equalTo($this->shipmentItemMock))
            ->will($this->returnValue('item-1'));
        $this->shipmentBuilderMock->expects($this->once())
            ->method('setItems')
            ->with($this->equalTo(['item-1']))
            ->will($this->returnSelf());


        $this->shipmentMock->expects($this->once())
            ->method('getTracksCollection')
            ->will($this->returnValue([$this->shipmentTrackMock]));
        $this->shipmentTrackMapperMock->expects($this->once())
            ->method('extractDto')
            ->with($this->equalTo($this->shipmentTrackMock))
            ->will($this->returnValue('track-1'));
        $this->shipmentBuilderMock->expects($this->once())
            ->method('setTracks')
            ->with($this->equalTo(['track-1']))
            ->will($this->returnSelf());

        $this->shipmentBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue('data-object-with-shipment'));
        $this->assertEquals('data-object-with-shipment', $this->shipmentMapper->extractDto($this->shipmentMock));
    }
}
