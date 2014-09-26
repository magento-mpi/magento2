<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Class CreditmemoReadTest
 */
class CreditmemoReadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\CreditmemoGet|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoGetMock;

    /**
     * @var \Magento\Sales\Service\V1\Action\CreditmemoList|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoListMock;

    /**
     * @var \Magento\Sales\Service\V1\Action\CreditmemoCommentsList|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoCommentsListMock;

    /**
     * @var \Magento\Sales\Service\V1\CreditmemoRead
     */
    protected $creditmemoRead;

    /**
     * SetUp
     */
    protected function setUp()
    {
        $this->creditmemoGetMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\CreditmemoGet',
            ['invoke'],
            [],
            '',
            false
        );
        $this->creditmemoListMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\CreditmemoList',
            ['invoke'],
            [],
            '',
            false
        );
        $this->creditmemoCommentsListMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\CreditmemoCommentsList',
            ['invoke'],
            [],
            '',
            false
        );

        $this->creditmemoRead = new CreditmemoRead(
            $this->creditmemoGetMock,
            $this->creditmemoListMock,
            $this->creditmemoCommentsListMock
        );
    }

    /**
     * test creditmemo get
     */
    public function testGet()
    {
        $this->creditmemoGetMock->expects($this->once())
            ->method('invoke')
            ->with(1)
            ->will($this->returnValue('creditmemo-do'));
        $this->assertEquals('creditmemo-do', $this->creditmemoRead->get(1));
    }

    /**
     * test creditmemo list
     */
    public function testSearch()
    {
        $searchCriteria = $this->getMock('Magento\Framework\Service\V1\Data\SearchCriteria', [], [], '', false);
        $this->creditmemoListMock->expects($this->once())
            ->method('invoke')
            ->with($searchCriteria)
            ->will($this->returnValue('search_result'));
        $this->assertEquals('search_result', $this->creditmemoRead->search($searchCriteria));
    }

    /**
     * test comments list
     */
    public function testCommentsList()
    {
        $this->creditmemoCommentsListMock->expects($this->once())
            ->method('invoke')
            ->with(1)
            ->will($this->returnValue('search_result'));
        $this->assertEquals('search_result', $this->creditmemoRead->commentsList(1));
    }
}
