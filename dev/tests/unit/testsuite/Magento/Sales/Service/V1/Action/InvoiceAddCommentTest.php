<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class InvoiceAddCommentTest
 */
class InvoiceAddCommentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\InvoiceAddComment
     */
    protected $invoiceAddComment;

    /**
     * @var \Magento\Sales\Model\Order\Invoice\CommentConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $commentConverterMock;

    /**
     * @var \Magento\Sales\Model\Order\Invoice\Comment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataModelMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\Invoice|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataObjectMock;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->commentConverterMock = $this->getMock(
            'Magento\Sales\Model\Order\Invoice\CommentConverter',
            ['getModel'],
            [],
            '',
            false
        );
        $this->dataModelMock = $this->getMock(
            'Magento\Sales\Model\Order\Invoice\Comment',
            ['save', '__wakeup'],
            [],
            '',
            false
        );
        $this->dataObjectMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\Comment',
            [],
            [],
            '',
            false
        );
        $this->invoiceAddComment = new InvoiceAddComment($this->commentConverterMock);
    }

    /**
     * Test invoice add comment service
     */
    public function testInvoke()
    {
        $this->commentConverterMock->expects($this->once())
            ->method('getModel')
            ->with($this->equalTo($this->dataObjectMock))
            ->will($this->returnValue($this->dataModelMock));
        $this->dataModelMock->expects($this->once())
            ->method('save');
        $this->assertTrue($this->invoiceAddComment->invoke($this->dataObjectMock));
    }
}
