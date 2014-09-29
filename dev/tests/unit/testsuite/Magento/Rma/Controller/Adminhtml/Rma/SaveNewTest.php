<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class SaveNewTest extends \Magento\Rma\Controller\Adminhtml\RmaTest
{
    protected $name = 'SaveNew';

    public function testSaveNewAction()
    {
        $dateTimeModelMock = $this->getMock('Magento\Framework\Stdlib\DateTime\DateTime', [], [], '', false);
        $commentText = 'some comment';
        $visibleOnFront = true;

        $expectedPost = $this->initRequestData($commentText, $visibleOnFront);

        $this->rmaDataMapperMock->expects($this->once())->method('filterRmaSaveRequest')
            ->with($expectedPost)
            ->will($this->returnValue($expectedPost));

        $this->coreRegistryMock->expects($this->once())
            ->method('registry')
            ->with('current_order')
            ->will($this->returnValue($this->orderMock));
        $this->rmaModelMock->expects($this->once())
            ->method('saveRma')
            ->will($this->returnSelf());
        $this->statusHistoryMock->expects($this->once())->method('sendNewRmaEmail');
        $this->statusHistoryMock->expects($this->once())
            ->method('saveComment')
            ->with($commentText, $visibleOnFront, true);
        $this->messageManagerMock->expects($this->once())
            ->method('addSuccess')
            ->with(__('You submitted the RMA request.'));

        $this->assertNull($this->action->execute());
    }
}
