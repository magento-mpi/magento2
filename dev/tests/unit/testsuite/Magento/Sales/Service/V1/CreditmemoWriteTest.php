<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Class CreditmemoWriteTest
 */
class CreditmemoWriteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\CreditmemoAddComment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoAddCommentMock;

    /**
     * @var \Magento\Sales\Service\V1\Action\CreditmemoCancel|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoCancelMock;

    /**
     * @var \Magento\Sales\Service\V1\Action\CreditmemoEmail|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoEmailMock;

    /**
     * @var \Magento\Sales\Service\V1\Action\CreditmemoCreate|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoCreateMock;

    /**
     * @var \Magento\Sales\Service\V1\CreditmemoWrite
     */
    protected $creditmemoWrite;

    /**
     * SetUp
     */
    protected function setUp()
    {
        $this->creditmemoAddCommentMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\CreditmemoAddComment',
            ['invoke'],
            [],
            '',
            false
        );
        $this->creditmemoCancelMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\CreditmemoCancel',
            ['invoke'],
            [],
            '',
            false
        );
        $this->creditmemoEmailMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\CreditmemoEmail',
            ['invoke'],
            [],
            '',
            false
        );

        $this->creditmemoCreateMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\CreditmemoCreate',
            ['invoke'],
            [],
            '',
            false
        );

        $this->creditmemoWrite = new CreditmemoWrite(
            $this->creditmemoAddCommentMock,
            $this->creditmemoCancelMock,
            $this->creditmemoEmailMock,
            $this->creditmemoCreateMock
        );
    }

    /**
     * test creditmemo add comment
     */
    public function testAddComment()
    {
        $comment = $this->getMock('Magento\Sales\Service\V1\Data\Comment', [], [], '', false);
        $this->creditmemoAddCommentMock->expects($this->once())
            ->method('invoke')
            ->with($comment)
            ->will($this->returnValue(true));
        $this->assertTrue($this->creditmemoWrite->addComment($comment));
    }

    /**
     * test creditmemo cancel
     */
    public function testCancel()
    {
        $this->creditmemoCancelMock->expects($this->once())
            ->method('invoke')
            ->with(1)
            ->will($this->returnValue(true));
        $this->assertTrue($this->creditmemoWrite->cancel(1));
    }

    /**
     * test creditmemo email
     */
    public function testEmail()
    {
        $this->creditmemoEmailMock->expects($this->once())
            ->method('invoke')
            ->with(1)
            ->will($this->returnValue(true));
        $this->assertTrue($this->creditmemoWrite->email(1));
    }

    /**
     * test creditmemo create
     */
    public function testCreate()
    {
        $creditmemo = $this->getMock('\Magento\Sales\Service\V1\Data\Creditmemo', [], [], '', false);
        $this->creditmemoCreateMock->expects($this->once())
            ->method('invoke')
            ->with($creditmemo)
            ->will($this->returnValue(true));
        $this->assertTrue($this->creditmemoWrite->create($creditmemo));
    }
}
