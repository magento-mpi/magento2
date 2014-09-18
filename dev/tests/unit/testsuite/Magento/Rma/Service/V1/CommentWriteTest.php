<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class CommentWriteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Service\V1\CommentWrite | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaServiceCommentWriteMock;

    /**
     * @var \Magento\Rma\Model\Rma\Status\History | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusHistoryMock;

    /**
     * @var \Magento\Rma\Model\RmaRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaRepositoryMock;

    protected function setUp()
    {
        $this->statusHistoryMock = $this->getMockBuilder('Magento\Rma\Model\Rma\Status\History')
            ->disableOriginalConstructor()
            ->setMethods(['setComment', 'setRma', 'sendCustomerCommentEmail', 'saveComment', '__wakeup'])
            ->getMock();

        $this->rmaRepositoryMock = $this->getMockBuilder('Magento\Rma\Model\RmaRepository')
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $this->rmaServiceCommentWriteMock = (new ObjectManagerHelper($this))->getObject(
            '\Magento\Rma\Service\V1\CommentWrite',
            [
                "rmaRepository" => $this->rmaRepositoryMock,
                "statusHistory" => $this->statusHistoryMock,
            ]
        );
    }

    /**
     * @expectedException        \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Please enter a valid comment.
     */
    public function testAddCommentException()
    {
        $dataMock = $this->getMockBuilder('\Magento\Rma\Service\V1\Data\RmaStatusHistory')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $dataMock
            ->expects($this->once())
            ->method('getComment')
            ->willReturn('');

        $this->rmaServiceCommentWriteMock->addComment(1, $dataMock);
    }

    /**
     * Data provider of success cases
     *
     * @see testAddCommentSuccess
     * @return array
     *
     * @case #1 All right and we send Customer Comment Email
     * @case #2 All right and we don't send Customer CommentEmail
     *
     */
    public function addCommentSuccessDataProvider()
    {
        return [
            1 => [1, "test comment", 1],
            2 => [1, "test comment", 0],
        ];
    }


    /**
     * Test for success cases
     *
     * @dataProvider addCommentSuccessDataProvider
     *
     * @param int    $id                  Rma id
     * @param string $commentText         Text of comment
     * @param int    $isCustomerNotified  Flag "is customer will be notified"
     *
     */
    public function testAddCommentSuccess($id, $commentText, $isCustomerNotified)
    {
        $dataMock = $this->getMockBuilder('\Magento\Rma\Service\V1\Data\RmaStatusHistory')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $dataMock->expects($this->once())->method('getComment')
            ->willReturn($commentText);

        $rmaMock = $this->getMockBuilder('Magento\Rma\Model\Rma')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup'])
            ->getMock();

        $this->rmaRepositoryMock->expects($this->once())->method('get')
            ->with($id)
            ->willReturn($rmaMock);

        $this->statusHistoryMock->expects($this->once())->method('setRma')
            ->with($rmaMock);

        $this->statusHistoryMock->expects($this->once())->method('setComment')
            ->with($commentText)
            ->willReturn($this->statusHistoryMock);

        $dataMock->expects($this->once())->method('isCustomerNotified')
            ->willReturn((boolean)$isCustomerNotified);

        $this->statusHistoryMock->expects($this->exactly($isCustomerNotified))->method('sendCustomerCommentEmail');

        $dataMock->expects($this->once())->method('isVisibleOnFront')
            ->willReturn(true);

        $this->statusHistoryMock->expects($this->once())->method('saveComment')
            ->with($commentText, true, true);

        $this->assertTrue($this->rmaServiceCommentWriteMock->addComment($id, $dataMock));
    }
}
 
