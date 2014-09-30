<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class ShipmentLabelGetTest
 */
class ShipmentLabelGetTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\ShipmentLabelGet
     */
    protected $shipmentLabelGet;

    /**
     * @var \Magento\Sales\Model\Order\ShipmentRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentRepositoryMock;

    /**
     * @var \Magento\Sales\Model\Order\Shipment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentMock;

    protected function setUp()
    {
        $this->shipmentRepositoryMock = $this->getMock(
            'Magento\Sales\Model\Order\ShipmentRepository',
            ['get', 'getShippingLabel'],
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
        $this->shipmentLabelGet = new ShipmentLabelGet(
            $this->shipmentRepositoryMock
        );
    }

    /**
     * test shipment label get service
     */
    public function testInvoke()
    {
        $this->shipmentRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->equalTo(1))
            ->will($this->returnValue($this->shipmentMock));
        $this->shipmentMock->expects($this->once())
            ->method('getShippingLabel')
            ->will($this->returnValue('shipping_label'));
        $this->assertEquals('shipping_label', $this->shipmentLabelGet->invoke(1));
    }
}
