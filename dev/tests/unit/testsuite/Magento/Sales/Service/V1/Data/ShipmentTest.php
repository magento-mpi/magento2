<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Data;

class ShipmentTest extends \PHPUnit_Framework_TestCase
{
    public function testGetBillingAddressId()
    {
        $data = ['billing_address_id' => 'test_value_billing_address_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Shipment($abstractBuilderMock);

        $this->assertEquals('test_value_billing_address_id', $object->getBillingAddressId());
    }

    public function testGetCreatedAt()
    {
        $data = ['created_at' => 'test_value_created_at'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Shipment($abstractBuilderMock);

        $this->assertEquals('test_value_created_at', $object->getCreatedAt());
    }

    public function testGetCustomerId()
    {
        $data = ['customer_id' => 'test_value_customer_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Shipment($abstractBuilderMock);

        $this->assertEquals('test_value_customer_id', $object->getCustomerId());
    }

    public function testGetEmailSent()
    {
        $data = ['email_sent' => 'test_value_email_sent'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Shipment($abstractBuilderMock);

        $this->assertEquals('test_value_email_sent', $object->getEmailSent());
    }

    public function testGetEntityId()
    {
        $data = ['entity_id' => 'test_value_entity_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Shipment($abstractBuilderMock);

        $this->assertEquals('test_value_entity_id', $object->getEntityId());
    }

    public function testGetIncrementId()
    {
        $data = ['increment_id' => 'test_value_increment_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Shipment($abstractBuilderMock);

        $this->assertEquals('test_value_increment_id', $object->getIncrementId());
    }

    public function testGetOrderId()
    {
        $data = ['order_id' => 'test_value_order_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Shipment($abstractBuilderMock);

        $this->assertEquals('test_value_order_id', $object->getOrderId());
    }

    public function testGetPackages()
    {
        $data = ['packages' => 'test_value_packages'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Shipment($abstractBuilderMock);

        $this->assertEquals('test_value_packages', $object->getPackages());
    }

    public function testGetShipmentStatus()
    {
        $data = ['shipment_status' => 'test_value_shipment_status'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Shipment($abstractBuilderMock);

        $this->assertEquals('test_value_shipment_status', $object->getShipmentStatus());
    }

    public function testGetShippingAddressId()
    {
        $data = ['shipping_address_id' => 'test_value_shipping_address_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Shipment($abstractBuilderMock);

        $this->assertEquals('test_value_shipping_address_id', $object->getShippingAddressId());
    }

    public function testGetShippingLabel()
    {
        $data = ['shipping_label' => 'test_value_shipping_label'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Shipment($abstractBuilderMock);

        $this->assertEquals('test_value_shipping_label', $object->getShippingLabel());
    }

    public function testGetStoreId()
    {
        $data = ['store_id' => 'test_value_store_id'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Shipment($abstractBuilderMock);

        $this->assertEquals('test_value_store_id', $object->getStoreId());
    }

    public function testGetTotalQty()
    {
        $data = ['total_qty' => 'test_value_total_qty'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Shipment($abstractBuilderMock);

        $this->assertEquals('test_value_total_qty', $object->getTotalQty());
    }

    public function testGetTotalWeight()
    {
        $data = ['total_weight' => 'test_value_total_weight'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Shipment($abstractBuilderMock);

        $this->assertEquals('test_value_total_weight', $object->getTotalWeight());
    }

    public function testGetUpdatedAt()
    {
        $data = ['updated_at' => 'test_value_updated_at'];
        $abstractBuilderMock = $this->getMockBuilder('Magento\Framework\Service\Data\AbstractObjectBuilder')
            ->setMethods(['getData'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $abstractBuilderMock->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data));

        $object = new \Magento\Sales\Service\V1\Data\Shipment($abstractBuilderMock);

        $this->assertEquals('test_value_updated_at', $object->getUpdatedAt());
    }
}
