<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Service\V1\Action;

/**
 * Test Class OrderEmailTest for Order Service
 */
class OrderEmailTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoke()
    {
        $orderId = 1;
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $orderRepository = $this->getMock('\Magento\Sales\Model\OrderRepository', ['get'], [], '', false);
        $notifier = $this->getMock('\Magento\Sales\Model\OrderNotifier', ['notify', '__wakeup'], [], '', false);
        $order = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['__wakeup', 'sendNewOrderEmail', 'getEmailSent'],
            [],
            '',
            false
        );

        $service = $objectManager->getObject(
            'Magento\Sales\Service\V1\Action\OrderEmail',
            [
                'orderRepository' => $orderRepository,
                'notifier' => $notifier
            ]
        );
        $orderRepository->expects($this->once())
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
 