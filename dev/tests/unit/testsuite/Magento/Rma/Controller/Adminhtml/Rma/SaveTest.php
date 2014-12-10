<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class SaveTest extends \Magento\Rma\Controller\Adminhtml\RmaTest
{
    protected $name = 'Save';

    public function testSaveAction()
    {
        $rmaId = 1;
        $commentText = 'some comment';
        $visibleOnFront = true;

        $this->requestMock->expects($this->any())
            ->method('getParam')
            ->will(
                $this->returnValueMap(
                    [
                        ['rma_id', null, $rmaId],
                    ]
                )
            );
        $expectedPost = $this->initRequestData($commentText, $visibleOnFront);

        $this->rmaDataMapperMock->expects($this->once())->method('filterRmaSaveRequest')
            ->with($expectedPost)
            ->will($this->returnValue($expectedPost));
        $this->rmaDataMapperMock->expects($this->once())->method('combineItemStatuses')
            ->with($expectedPost['items'], $rmaId)
            ->will($this->returnValue([]));

        $this->rmaModelMock->expects($this->once())
            ->method('getId')
            ->will($this->returnValue($rmaId));
        $this->rmaModelMock->expects($this->any())
            ->method('setStatus')
            ->will($this->returnSelf());
        $this->rmaModelMock->expects($this->once())
            ->method('saveRma')
            ->will($this->returnSelf());
        $this->statusHistoryMock->expects($this->once())
            ->method('setRma')
            ->with($this->rmaModelMock);
        $this->statusHistoryMock->expects($this->once())->method('sendAuthorizeEmail');
        $this->statusHistoryMock->expects($this->once())
            ->method('saveSystemComment');

        $this->assertNull($this->action->execute());
    }
}
