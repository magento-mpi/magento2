<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Controller\Guest;

class AddCommentTest extends \Magento\Rma\Controller\GuestTest
{
    /**
     * @var string
     */
    protected $name = 'AddComment';

    public function testAddCommentAction()
    {
        $entityId = 7;
        $orderId = 5;
        $comment = 'comment';

        $this->request->expects($this->any())
            ->method('getParam')
            ->with('entity_id')
            ->will($this->returnValue($entityId));
        $this->request->expects($this->any())
            ->method('getPost')
            ->with('comment')
            ->will($this->returnValue($comment));

        $rmaHelper = $this->getMock('Magento\Rma\Helper\Data', [], [], '', false);
        $rmaHelper->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $guestHelper = $this->getMock('Magento\Sales\Helper\Guest', [], [], '', false);
        $guestHelper->expects($this->once())
            ->method('loadValidOrder')
            ->with($this->request, $this->response)
            ->will($this->returnValue(true));

        $rma = $this->getMock(
            'Magento\Rma\Model\Rma',
            ['__wakeup', 'load', 'getCustomerId', 'getId', 'getOrderId'],
            [],
            '',
            false
        );
        $rma->expects($this->once())
            ->method('load')
            ->with($entityId)
            ->will($this->returnSelf());
        $rma->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($entityId));
        $rma->expects($this->any())
            ->method('getOrderId')
            ->will($this->returnValue($orderId));

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

        $history = $this->getMock('Magento\Rma\Model\Rma\Status\History', [], [], '', false);
        $history->expects($this->once())
            ->method('sendCustomerCommentEmail');
        $history->expects($this->once())
            ->method('saveComment')
            ->with($comment, true, false);

        $this->objectManager->expects($this->at(0))
            ->method('get')
            ->with('Magento\Rma\Helper\Data')
            ->will($this->returnValue($rmaHelper));
        $this->objectManager->expects($this->at(1))
            ->method('get')
            ->with('Magento\Sales\Helper\Guest')
            ->will($this->returnValue($guestHelper));
        $this->objectManager->expects($this->at(2))
            ->method('create')
            ->with('Magento\Rma\Model\Rma')
            ->will($this->returnValue($rma));
        $this->objectManager->expects($this->at(3))
            ->method('create')
            ->with('Magento\Rma\Model\Rma\Status\History')
            ->will($this->returnValue($history));

        $this->coreRegistry->expects($this->at(0))
            ->method('registry')
            ->with('current_order')
            ->will($this->returnValue($order));
        $this->coreRegistry->expects($this->at(1))
            ->method('registry')
            ->with('current_rma')
            ->will($this->returnValue($rma));

        $this->controller->execute();
    }
}
