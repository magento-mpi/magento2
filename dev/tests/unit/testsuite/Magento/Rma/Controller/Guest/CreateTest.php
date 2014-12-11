<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Controller\Guest;

class CreateTest extends \Magento\Rma\Controller\GuestTest
{
    /**
     * @var string
     */
    protected $name = 'Create';

    public function testCreateAction()
    {
        $orderId = 2;
        $post = ['customer_custom_email' => true, 'items' => ['1', '2'], 'rma_comment' => 'comment'];

        $order = $this->getMock(
            'Magento\Sales\Model\Order',
            ['__wakeup', 'getCustomerId', 'load', 'getId'],
            [],
            '',
            false
        );
        $order->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($orderId));

        $dateTime = $this->getMock('Magento\Framework\Stdlib\DateTime\DateTime', [], [], '', false);
        $rma = $this->getMock('Magento\Rma\Model\Rma', [], [], '', false);
        $rma->expects($this->once())
            ->method('setData')
            ->will($this->returnSelf());
        $rma->expects($this->once())
            ->method('saveRma')
            ->will($this->returnSelf());
        $history1 = $this->getMock('Magento\Rma\Model\Rma\Status\History', [], [], '', false);
        $history2 = $this->getMock('Magento\Rma\Model\Rma\Status\History', [], [], '', false);
        $rmaHelper = $this->getMock('Magento\Rma\Helper\Data', [], [], '', false);
        $rmaHelper->expects($this->once())
            ->method('canCreateRma')
            ->with($orderId)
            ->will($this->returnValue(true));
        $guestHelper = $this->getMock('Magento\Sales\Helper\Guest', [], [], '', false);
        $guestHelper->expects($this->once())
            ->method('loadValidOrder')
            ->with($this->request, $this->response)
            ->will($this->returnValue(true));

        $this->objectManager->expects($this->at(0))
            ->method('get')
            ->with('Magento\Sales\Helper\Guest')
            ->will($this->returnValue($guestHelper));
        $this->objectManager->expects($this->at(1))
            ->method('get')
            ->with('Magento\Rma\Helper\Data')
            ->will($this->returnValue($rmaHelper));
        $this->objectManager->expects($this->at(2))
            ->method('get')
            ->with('Magento\Framework\Stdlib\DateTime\DateTime')
            ->will($this->returnValue($dateTime));
        $this->objectManager->expects($this->at(3))
            ->method('create')
            ->with('Magento\Rma\Model\Rma')
            ->will($this->returnValue($rma));
        $this->objectManager->expects($this->at(4))
            ->method('create')
            ->with('Magento\Rma\Model\Rma\Status\History')
            ->will($this->returnValue($history1));
        $this->objectManager->expects($this->at(5))
            ->method('create')
            ->with('Magento\Rma\Model\Rma\Status\History')
            ->will($this->returnValue($history2));

        $this->request->expects($this->once())
            ->method('getPost')
            ->will($this->returnValue($post));
        $this->coreRegistry->expects($this->once())
            ->method('registry')
            ->with('current_order')
            ->will($this->returnValue($order));

        $this->controller->execute();
    }
}
