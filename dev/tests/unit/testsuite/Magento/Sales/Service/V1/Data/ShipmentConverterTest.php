<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

/**
 * Class ShipmentConverterTest
 * @package Magento\Sales\Service\V1\Data
 */
class ShipmentConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $shipmentLoaderMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\ShipmentConverter
     */
    protected $converter;

    public function setUp()
    {
        $this->shipmentLoaderMock = $this->getMockBuilder('Magento\Shipping\Controller\Adminhtml\Order\ShipmentLoader')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $this->converter = new \Magento\Sales\Service\V1\Data\ShipmentConverter($this->shipmentLoaderMock);
    }

    public function testGetModel()
    {
        $orderId = 1;
        $shipmentId = 2;
        $items = [];
        $tracking = [];

        $shipmentDataObjectMock = $this->getMockBuilder('Magento\Sales\Service\V1\Data\Shipment')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();
        $shipmentDataObjectMock->expects($this->any())
            ->method('getOrderId')
            ->will($this->returnValue($orderId));
        $shipmentDataObjectMock->expects($this->any())
            ->method('getEntityId')
            ->will($this->returnValue($shipmentId));
        $shipmentDataObjectMock->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue($items));
        $shipmentDataObjectMock->expects($this->any())
            ->method('getTracks')
            ->will($this->returnValue($tracking));

        $shipmentMock = $this->getMockBuilder('Magento\Sales\Model\Order\Shipment')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->shipmentLoaderMock->expects($this->once())
            ->method('load')
            ->with()
            ->will($this->returnValue($shipmentMock));

        $this->assertInstanceOf(
            'Magento\Sales\Model\Order\Shipment',
            $this->converter->getModel($shipmentDataObjectMock)
        );
    }
}
