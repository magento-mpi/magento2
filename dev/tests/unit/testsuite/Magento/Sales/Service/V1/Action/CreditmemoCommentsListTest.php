<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1\Action;

/**
 * Class CreditmemoCommentsListTest
 */
class CreditmemoCommentsListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Sales\Service\V1\Action\CreditmemoCommentsList
     */
    protected $creditmemoCommentsList;

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo\CommentRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $commentRepositoryMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\CommentMapper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $commentMapperMock;

    /**
     * @var \Magento\Framework\Data\SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $criteriaBuilderMock;

    /**
     * @var \Magento\Framework\Service\V1\Data\FilterBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterBuilderMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\CommentSearchResultsBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchResultsBuilderMock;

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo\Comment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoCommentMock;

    /**
     * @var \Magento\Sales\Service\V1\Data\Comment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataObjectMock;

    /**
     * @var \Magento\Framework\Data\SearchCriteria|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchCriteriaMock;

    protected function setUp()
    {
        $this->commentRepositoryMock = $this->getMock(
            'Magento\Sales\Model\Order\Creditmemo\CommentRepository',
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
            'Magento\Framework\Data\SearchCriteriaBuilder',
            ['create', 'addFilter'],
            [],
            '',
            false
        );
        $this->filterBuilderMock = $this->getMock(
            'Magento\Framework\Service\V1\Data\FilterBuilder',
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
        $this->creditmemoCommentMock = $this->getMock(
            'Magento\Sales\Model\Order\Creditmemo\Comment',
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
            'Magento\Framework\Data\SearchCriteria',
            [],
            [],
            '',
            false
        );
        $this->creditmemoCommentsList = new CreditmemoCommentsList(
            $this->commentRepositoryMock,
            $this->commentMapperMock,
            $this->criteriaBuilderMock,
            $this->filterBuilderMock,
            $this->searchResultsBuilderMock
        );

    }

    /**
     * test creditmemo comments list service
     */
    public function testInvoke()
    {
        $creditmemoId = 1;
        $this->filterBuilderMock->expects($this->once())
            ->method('setField')
            ->with($this->equalTo('parent_id'))
            ->will($this->returnSelf());
        $this->filterBuilderMock->expects($this->once())
            ->method('setValue')
            ->with($this->equalTo($creditmemoId))
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
            ->will($this->returnValue([$this->creditmemoCommentMock]));

        $this->commentMapperMock->expects($this->once())
            ->method('extractDto')
            ->with($this->equalTo($this->creditmemoCommentMock))
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

        $this->assertEquals('expected-result', $this->creditmemoCommentsList->invoke($creditmemoId));
    }
}
