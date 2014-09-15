<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class ShipmentGetTest
 */
class ShipmentGetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\ShipmentGet
     */
    protected $shipmentGet;

    /**
     * @var \Magento\Sales\Model\Order\ShipmentRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentRepositoryMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\ShipmentMapper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentMapperMock;

    /**
     * @var \Magento\Sales\Model\Order\Shipment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\Shipment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataObjectMock;

    /**
     * SetUp
     */
    protected function setUp()
    {
        $this->shipmentRepositoryMock = $this->getMock(
            'Magento\Sales\Model\Order\ShipmentRepository',
            ['get'],
            [],
            '',
            false
        );
        $this->shipmentMapperMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\ShipmentMapper',
            [],
            [],
            '',
            false
        );
        $this->searchResultsBuilderMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\ShipmentSearchResultsBuilder',
            [],
            [],
            '',
            false
        );
        $this->shipmentMock = $this->getMock(
            'Magento\Sales\Model\Order\Shipment',
            [],
            [],
            '',
            false
        );
        $this->dataObjectMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\Shipment',
            [],
            [],
            '',
            false
        );
        $this->shipmentGet = new ShipmentGet(
            $this->shipmentRepositoryMock,
            $this->shipmentMapperMock
        );
    }

    /**
     * test shipment get service
     */
    public function testInvoke()
    {
        $this->shipmentRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->shipmentMock));
        $this->shipmentMapperMock->expects($this->once())
            ->method('extractDto')
            ->with($this->equalTo($this->shipmentMock))
            ->will($this->returnValue($this->dataObjectMock));
        $this->assertEquals($this->dataObjectMock, $this->shipmentGet->invoke(1));
    }
}
