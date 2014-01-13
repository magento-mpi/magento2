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

        $carrier = $this->getMock('Magento\Shipping\Model\Carrier\Freeshipping',
            ['setStore', 'getTrackingInfo'], [], '', false);
        $carrier->expects($this->once())->method('setStore')->with('');
        $carrier->expects($this->once())->method('getTrackingInfo')
            ->will($this->returnValue('trackingInfo'));

        $config = $this->getMock('\Magento\Shipping\Model\Config', ['getCarrierInstance'], [], '', false);
        $config->expects($this->once())->method('getCarrierInstance')
            ->will($this->returnValue($carrier));

        $shipment = $this->getMock('Magento\Shipping\Model\Carrier\Freeshipping', ['load'], [], '', false);
        $shipment->expects($this->any())->method('load')
            ->will($this->returnValue($shipment));

        $shipmentFactory = $this->getMock('\Magento\Sales\Model\Order\ShipmentFactory', ['create'], [], '', false);
        $shipmentFactory->expects($this->any())->method('create')
            ->will($this->returnValue($shipment));

        /** @var \Magento\Shipping\Model\Order\Track $model */
        $model = $helper->getObject(
            'Magento\Shipping\Model\Order\Track',
            [
                'shippingConfig' => $config,
                'shipmentFactory' => $shipmentFactory
            ]
        );

        $this->assertEquals('trackingInfo', $model->getNumberDetail());
    }
}
