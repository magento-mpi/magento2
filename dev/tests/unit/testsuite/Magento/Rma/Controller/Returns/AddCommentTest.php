<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Controller\Returns;

class AddCommentTest extends \Magento\Rma\Controller\ReturnsTest
{
    /**
     * @var string
     */
    protected $name = 'AddComment';

    public function testAddCommentAction()
    {
        $entityId = 7;
        $customerId = 8;
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

        $rma = $this->getMock('Magento\Rma\Model\Rma', ['__wakeup', 'load', 'getCustomerId', 'getId'], [], '', false);
        $rma->expects($this->once())
            ->method('load')
            ->with($entityId)
            ->will($this->returnSelf());
        $rma->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($entityId));
        $rma->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue($customerId));

        $session = $this->getMock('Magento\Customer\Model\Session', [], [], '', false);
        $session->expects($this->once())
            ->method('getCustomerId')
            ->will($this->returnValue($customerId));

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
            ->method('create')
            ->with('Magento\Rma\Model\Rma')
            ->will($this->returnValue($rma));
        $this->objectManager->expects($this->at(2))
            ->method('get')
            ->with('Magento\Customer\Model\Session')
            ->will($this->returnValue($session));
        $this->objectManager->expects($this->at(3))
            ->method('create')
            ->with('Magento\Rma\Model\Rma\Status\History')
            ->will($this->returnValue($history));

        $this->controller->execute();
    }
}
