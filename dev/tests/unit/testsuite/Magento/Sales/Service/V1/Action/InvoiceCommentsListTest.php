<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class InvoiceCommentsListTest
 */
class InvoiceCommentsListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\InvoiceCommentsList
     */
    protected $invoiceCommentsList;

    /**
     * @var \Magento\Sales\Model\Order\Invoice\CommentRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $commentRepositoryMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\CommentMapper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $commentMapperMock;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $criteriaBuilderMock;

    /**
     * @var \Magento\Framework\Api\FilterBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterBuilderMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\CommentSearchResultsBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchResultsBuilderMock;

    /**
     * @var \Magento\Sales\Model\Order\Invoice\Comment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invoiceCommentMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\Comment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataObjectMock;

    /**
     * @var \Magento\Framework\Api\SearchCriteria|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchCriteriaMock;

    protected function setUp()
    {
        $this->commentRepositoryMock = $this->getMock(
            'Magento\Sales\Model\Order\Invoice\CommentRepository',
            ['find'],
            [],
            '',
            false
        );
        $this->commentMapperMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\CommentMapper',
            ['extractDto'],
            [],
            '',
            false
        );
        $this->criteriaBuilderMock = $this->getMock(
            'Magento\Framework\Api\SearchCriteriaBuilder',
            ['create', 'addFilter'],
            [],
            '',
            false
        );
        $this->filterBuilderMock = $this->getMock(
            'Magento\Framework\Api\FilterBuilder',
            ['setField', 'setValue', 'create'],
            [],
            '',
            false
        );
        $this->searchResultsBuilderMock = $this->getMock(
            'Magento\Sales\Service\V1\Data\CommentSearchResultsBuilder',
            ['setItems', 'setSearchCriteria', 'create', 'setTotalCount'],
            [],
            '',
            false
        );
        $this->invoiceCommentMock = $this->getMock(
            'Magento\Sales\Model\Order\Invoice\Comment',
            [],
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
        $this->searchCriteriaMock = $this->getMock(
            'Magento\Framework\Api\SearchCriteria',
            [],
            [],
            '',
            false
        );
        $this->invoiceCommentsList = new InvoiceCommentsList(
            $this->commentRepositoryMock,
            $this->commentMapperMock,
            $this->criteriaBuilderMock,
            $this->filterBuilderMock,
            $this->searchResultsBuilderMock
        );

    }

    /**
     * test invoice comments list service
     */
    public function testInvoke()
    {
        $invoiceId = 1;
        $this->filterBuilderMock->expects($this->once())
            ->method('setField')
            ->with($this->equalTo('parent_id'))
            ->will($this->returnSelf());
        $this->filterBuilderMock->expects($this->once())
            ->method('setValue')
            ->with($this->equalTo($invoiceId))
            ->will($this->returnSelf());
        $this->filterBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue('filter'));
        $this->criteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with($this->equalTo(['eq' => 'filter']))
            ->will($this->returnSelf());
        $this->criteriaBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->searchCriteriaMock));

        $this->commentRepositoryMock->expects($this->once())
            ->method('find')
            ->with($this->equalTo($this->searchCriteriaMock))
            ->will($this->returnValue([$this->invoiceCommentMock]));

        $this->commentMapperMock->expects($this->once())
            ->method('extractDto')
            ->with($this->equalTo($this->invoiceCommentMock))
            ->will($this->returnValue($this->dataObjectMock));

        $this->searchResultsBuilderMock->expects($this->once())
            ->method('setItems')
            ->with($this->equalTo([$this->dataObjectMock]))
            ->will($this->returnSelf());
        $this->searchResultsBuilderMock->expects($this->once())
            ->method('setTotalCount')
            ->with($this->equalTo(1))
            ->will($this->returnSelf());
        $this->searchResultsBuilderMock->expects($this->once())
            ->method('setSearchCriteria')
            ->with($this->equalTo($this->searchCriteriaMock))
            ->will($this->returnSelf());
        $this->searchResultsBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue('expected-result'));

        $this->assertEquals('expected-result', $this->invoiceCommentsList->invoke($invoiceId));
    }
}
