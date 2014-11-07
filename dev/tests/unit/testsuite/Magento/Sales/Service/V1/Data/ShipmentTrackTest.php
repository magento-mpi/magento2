<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

class ShipmentTrackTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCarrierCode()
    {
        $data = ['carrier_code' => 'test_value_carrier_code'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Api\ExtensibleObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\ShipmentTrack($abstractBuilderMock);

        $this->assertEquals('test_value_carrier_code', $object->getCarrierCode());
    }

    public function testGetCreatedAt()
    {
        $data = ['created_at' => 'test_value_created_at'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Api\ExtensibleObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\ShipmentTrack($abstractBuilderMock);

        $this->assertEquals('test_value_created_at', $object->getCreatedAt());
    }

    public function testGetDescription()
    {
        $data = ['description' => 'test_value_description'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Api\ExtensibleObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\ShipmentTrack($abstractBuilderMock);

        $this->assertEquals('test_value_description', $object->getDescription());
    }

    public function testGetEntityId()
    {
        $data = ['entity_id' => 'test_value_entity_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Api\ExtensibleObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\ShipmentTrack($abstractBuilderMock);

        $this->assertEquals('test_value_entity_id', $object->getEntityId());
    }

    public function testGetOrderId()
    {
        $data = ['order_id' => 'test_value_order_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Api\ExtensibleObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\ShipmentTrack($abstractBuilderMock);

        $this->assertEquals('test_value_order_id', $object->getOrderId());
    }

    public function testGetParentId()
    {
        $data = ['parent_id' => 'test_value_parent_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Api\ExtensibleObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\ShipmentTrack($abstractBuilderMock);

        $this->assertEquals('test_value_parent_id', $object->getParentId());
    }

    public function testGetQty()
    {
        $data = ['qty' => 'test_value_qty'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Api\ExtensibleObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\ShipmentTrack($abstractBuilderMock);

        $this->assertEquals('test_value_qty', $object->getQty());
    }

    public function testGetTitle()
    {
        $data = ['title' => 'test_value_title'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Api\ExtensibleObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\ShipmentTrack($abstractBuilderMock);

        $this->assertEquals('test_value_title', $object->getTitle());
    }

    public function testGetTrackNumber()
    {
        $data = ['track_number' => 'test_value_track_number'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Api\ExtensibleObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\ShipmentTrack($abstractBuilderMock);

        $this->assertEquals('test_value_track_number', $object->getTrackNumber());
    }

    public function testGetUpdatedAt()
    {
        $data = ['updated_at' => 'test_value_updated_at'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Api\ExtensibleObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\ShipmentTrack($abstractBuilderMock);

        $this->assertEquals('test_value_updated_at', $object->getUpdatedAt());
    }

    public function testGetWeight()
    {
        $data = ['weight' => 'test_value_weight'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Api\ExtensibleObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\ShipmentTrack($abstractBuilderMock);

        $this->assertEquals('test_value_weight', $object->getWeight());
    }
}
