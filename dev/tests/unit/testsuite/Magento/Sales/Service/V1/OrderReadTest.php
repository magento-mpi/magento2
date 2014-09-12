<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Class OrderReadTest
 */
class OrderReadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\OrderGet|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderGetMock;

    /**
     * @var \Magento\Sales\Service\V1\Action\OrderList|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderListMock;

    /**
     * @var \Magento\Sales\Service\V1\Action\OrderCommentsList|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCommentsListMock;

    /**
     * @var \Magento\Sales\Service\V1\Action\OrderGetStatus|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderGetStatusMock;

    /**
     * @var \Magento\Sales\Service\V1\OrderRead
     */
    protected $orderRead;

    /**
     * SetUp
     */
    protected function setUp()
    {
        $this->orderGetMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\OrderGet',
            ['invoke'],
            [],
            '',
            false
        );
        $this->orderListMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\OrderList',
            ['invoke'],
            [],
            '',
            false
        );
        $this->orderCommentsListMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\OrderCommentsList',
            ['invoke'],
            [],
            '',
            false
        );
        $this->orderGetStatusMock = $this->getMock(
            'Magento\Sales\Service\V1\Action\OrderGetStatus',
            ['invoke'],
            [],
            '',
            false
        );

        $this->orderRead = new OrderRead(
            $this->orderGetMock,
            $this->orderListMock,
            $this->orderCommentsListMock,
            $this->orderGetStatusMock
        );
    }

    /**
     * test order get
     */
    public function testGet()
    {
        $this->orderGetMock->expects($this->once())
            ->method('invoke')
            ->with(1)
            ->will($this->returnValue('order-do'));
        $this->assertEquals('order-do', $this->orderRead->get(1));
    }

    /**
     * test order list
     */
    public function testSearch()
    {
        $searchCriteria = $this->getMock('Magento\Framework\Service\V1\Data\SearchCriteria', [], [], '', false);
        $this->orderListMock->expects($this->once())
            ->method('invoke')
            ->with($searchCriteria)
            ->will($this->returnValue('search_result'));
        $this->assertEquals('search_result', $this->orderRead->search($searchCriteria));
    }

    /**
     * test order comments list
     */
    public function testCommentsList()
    {
        $this->orderCommentsListMock->expects($this->once())
            ->method('invoke')
            ->with(1)
            ->will($this->returnValue('search_result'));
        $this->assertEquals('search_result', $this->orderRead->commentsList(1));
    }

    /**
     * test order get status
     */
    public function testGetStatus()
    {
        $this->orderGetStatusMock->expects($this->once())
            ->method('invoke')
            ->with(1)
            ->will($this->returnValue('search_result'));
        $this->assertEquals('search_result', $this->orderRead->getStatus(1));
    }
}
