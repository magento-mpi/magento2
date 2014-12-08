<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class CloseTest extends \Magento\Rma\Controller\Adminhtml\RmaTest
{
    protected $name = 'Close';

    public function testCloseAction()
    {
        $entityId = 1;
        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->will(
                $this->returnValueMap(
                    [
                        ['entity_id', null, $entityId],
                    ]
                )
            );
        $this->rmaModelMock->expects($this->once())
            ->method('load')
            ->with($entityId)
            ->will($this->returnSelf());
        $this->rmaModelMock->expects($this->once())
            ->method('canClose')
            ->will($this->returnValue(true));
        $this->rmaModelMock->expects($this->once())
            ->method('close')
            ->will($this->returnSelf());
        $this->statusHistoryMock->expects($this->once())
            ->method('setRma')
            ->with($this->rmaModelMock);
        $this->statusHistoryMock->expects($this->once())
            ->method('saveSystemComment');

        $this->assertNull($this->action->execute());
    }
}
