<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shipping\Model\Order;

class TrackTest extends \PHPUnit_Framework_TestCase
{
    public function testLookup()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $carrier = $this->getMock(
            'Magento\OfflineShipping\Model\Carrier\Freeshipping',
            array('setStore', 'getTrackingInfo'),
            array(),
            '',
            false
        );
        $carrier->expects($this->once())->method('setStore')->with('');
        $carrier->expects($this->once())->method('getTrackingInfo')->will($this->returnValue('trackingInfo'));

        $carrierFactory = $this->getMock(
            '\Magento\Shipping\Model\CarrierFactory',
            array('create'),
            array(),
            '',
            false
        );
        $carrierFactory->expects($this->once())->method('create')->will($this->returnValue($carrier));

        $shipment = $this->getMock(
            'Magento\OfflineShipping\Model\Carrier\Freeshipping',
            array('load'),
            array(),
            '',
            false
        );
        $shipment->expects($this->any())->method('load')->will($this->returnValue($shipment));

        $shipmentFactory = $this->getMock(
            '\Magento\Sales\Model\Order\ShipmentFactory',
            array('create'),
            array(),
            '',
            false
        );
        $shipmentFactory->expects($this->any())->method('create')->will($this->returnValue($shipment));

        /** @var \Magento\Shipping\Model\Order\Track $model */
        $model = $helper->getObject(
            'Magento\Shipping\Model\Order\Track',
            array('carrierFactory' => $carrierFactory, 'shipmentFactory' => $shipmentFactory)
        );

        $this->assertEquals('trackingInfo', $model->getNumberDetail());
    }
}
