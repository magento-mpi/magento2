<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Controller\Adminhtml\Rma;

class AddCommentTest extends \Magento\Rma\Controller\Adminhtml\RmaTest
{
    protected $name = 'AddComment';

    public function testAddCommentsAction()
    {
        $commentText = 'some comment';
        $visibleOnFront = true;
        $blockContents = [
            $commentText,
        ];
        $layoutMock = $this->getMock('Magento\Framework\View\LayoutInterface', [], [], '', false);
        $blockMock = $this->getMock('Magento\Framework\View\Element\BlockInterface', [], [], '', false);
        $coreHelperMock = $this->getMock('Magento\Core\Helper\Data', [], [], '', false);

        $this->requestMock->expects($this->once())
            ->method('getPost')
            ->will(
                $this->returnValue(
                    [
                        'comment' => $commentText,
                        'is_visible_on_front' => $visibleOnFront,
                        'is_customer_notified' => true,
                    ]
                )
            );
        $this->coreRegistryMock->expects($this->once())
            ->method('registry')
            ->with('current_rma')
            ->will($this->returnValue($this->rmaModelMock));
        $this->statusHistoryMock->expects($this->once())
            ->method('setRma')
            ->with($this->rmaModelMock);
        $this->statusHistoryMock->expects($this->once())
            ->method('setComment')
            ->with($commentText);
        $this->viewMock->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layoutMock));
        $layoutMock->expects($this->once())
            ->method('getBlock')
            ->with('comments_history')
            ->will($this->returnValue($blockMock));
        $blockMock->expects($this->once())
            ->method('toHtml')
            ->will($this->returnValue($blockContents));
        $this->objectManagerMock->expects($this->once())
            ->method('get')
            ->with('Magento\Core\Helper\Data')
            ->will($this->returnValue($coreHelperMock));
        $coreHelperMock->expects($this->once())
            ->method('jsonEncode')
            ->will($this->returnValue($commentText));

        $this->responseMock->expects($this->once())
            ->method('representJson')
            ->with($commentText);

        $this->assertNull($this->action->execute());
    }
}
