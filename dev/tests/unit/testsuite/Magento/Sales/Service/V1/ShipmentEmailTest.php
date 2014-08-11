<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1;

/**
 * Test Class ShipmentEmailTest for Shipment Service
 *
 * @package Magento\Sales\Service\V1
 */
class ShipmentEmailTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoke()
    {
        $orderId = 1;
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $shipmentRepository = $this->getMock('\Magento\Sales\Model\ShipmentRepository', ['get'], [], '', false);
        $notifier = $this->getMock('\Magento\Shipping\Model\ShipmentNotifier', ['notify', '__wakeup'], [], '', false);
        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['__wakeup', 'sendNewOrderEmail', 'getEmailSent'],
            [],
            '',
            false
        );

        $service = $objectManager->getObject(
            'Magento\Sales\Service\V1\ShipmentEmail',
            [
                'shipmentRepository' => $shipmentRepository,
                'notifier' => $notifier
            ]
        );
        $shipmentRepository->expects($this->once())
            ->method('get')
            ->with($orderId)
            ->will($this->returnValue($order));
        $notifier->expects($this->any())
            ->method('notify')
            ->with($order)
            ->will($this->returnValue(true));
        $this->assertTrue($service->invoke($orderId));
    }
}
 