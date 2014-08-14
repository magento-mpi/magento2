<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class ShipmentCreateTest
 */
class ShipmentCreateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\ShipmentCreate
     */
    protected $shipmentCreate;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentConverterMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $loggerMock;

    public function setUp()
    {
        $this->shipmentConverterMock = $this->getMockBuilder('Magento\Sales\Service\V1\Data\ShipmentConverter')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->loggerMock = $this->getMockBuilder('Magento\Framework\Logger')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->shipmentCreate = new ShipmentCreate(
            $this->shipmentConverterMock,
            $this->loggerMock
        );
    }

    public function testInvoke()
    {
        $orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $orderMock->expects($this->any())
            ->method('setIsInProcess')
            ->with(true);
        $shipmentMock = $this->getMockBuilder('Magento\Sales\Model\Order\Shipment')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $shipmentMock->expects($this->once())
            ->method('getOrder')
            ->will($this->returnValue($orderMock));
        $shipmentMock->expects($this->once())
            ->method('register');
        $shipmentMock->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));
        $shipmentDataObjectMock = $this->getMockBuilder('Magento\Sales\Service\V1\Data\Shipment')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->shipmentConverterMock->expects($this->once())
            ->method('getModel')
            ->with($shipmentDataObjectMock)
            ->will($this->returnValue($shipmentMock));
        $this->assertTrue($this->shipmentCreate->invoke($shipmentDataObjectMock));
    }

    public function testInvokeNoShipment()
    {
        $shipmentDataObjectMock = $this->getMockBuilder('Magento\Sales\Service\V1\Data\Shipment')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->shipmentConverterMock->expects($this->once())
            ->method('getModel')
            ->with($shipmentDataObjectMock)
            ->will($this->returnValue(false));
        $this->assertFalse($this->shipmentCreate->invoke($shipmentDataObjectMock));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage An error has occurred during creating Shipment
     */
    public function testInvokeException()
    {
        $message = 'Can not save Shipment';
        $e = new \Exception($message);

        $shipmentDataObjectMock = $this->getMockBuilder('Magento\Sales\Service\V1\Data\Shipment')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->loggerMock->expects($this->once())
            ->method('logException')
            ->with($e);
        $this->shipmentConverterMock->expects($this->once())
            ->method('getModel')
            ->with($shipmentDataObjectMock)
            ->will($this->throwException($e));
        $this->shipmentCreate->invoke($shipmentDataObjectMock);
    }
}
