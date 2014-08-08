<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Class CreditmemoAddCommentTest
 */
class CreditmemoAddCommentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\CreditmemoAddComment
     */
    protected $creditmemoAddComment;

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo\CommentConverter|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $commentConverterMock;

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo\Comment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataModelMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\Creditmemo|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataObjectMock;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->commentConverterMock = $this->getMock(
            'Magento\Sales\Model\Order\Creditmemo\CommentConverter',
            ['getModel'],
            [],
            '',
            false
        );
        $this->dataModelMock = $this->getMock(
            'Magento\Sales\Model\Order\Creditmemo\Comment',
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
        $this->creditmemoAddComment = new \Magento\Sales\Service\V1\CreditmemoAddComment($this->commentConverterMock);
    }

    /**
     * Test creditmemo add comment service
     */
    public function testInvoke()
    {
        $this->commentConverterMock->expects($this->once())
            ->method('getModel')
            ->with($this->equalTo($this->dataObjectMock))
            ->will($this->returnValue($this->dataModelMock));
        $this->dataModelMock->expects($this->once())
            ->method('save');
        $this->assertTrue($this->creditmemoAddComment->invoke($this->dataObjectMock));
    }
}
