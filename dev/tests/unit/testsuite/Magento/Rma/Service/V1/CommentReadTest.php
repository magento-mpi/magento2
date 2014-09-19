<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Service\V1;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class CommentReadTest
 *
 * @package Magento\Rma\Service\V1
 */
class CommentReadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  \Magento\Rma\Service\V1\CommentRead | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaServiceCommentReadMock;

    /**
     * @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $criteriaBuilderMock;

    /**
     * @var \Magento\Framework\Service\V1\Data\FilterBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterBuilderMock;

    /**
     * @var \Magento\Framework\Service\V1\Data\Filter | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $filterMock;

    /**
     * @var \Magento\Rma\Model\Rma\Status\HistoryRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $historyRepositoryMock;

    /**
     * @var \Magento\Rma\Service\V1\Data\RmaStatusHistoryMapper | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $historyMapperMock;

    /**
     * @var \Magento\Rma\Service\V1\Data\RmaStatusHistorySearchResultsBuilder | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchResultsBuilderMock;

    /**
     * @var \Magento\Framework\Service\V1\Data\SearchCriteria | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchCriteriaMock;

    /**
     * @var \Magento\Rma\Service\V1\Data\RmaStatusHistory | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataObjectMock;

    /**
     * @var \Magento\Rma\Model\Rma\Status\History | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $commentMock;

    /**
     * @var \Magento\Rma\Service\V1\Data\RmaStatusHistorySearchResults | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $searchResultsMock;

    /**
     * @var \Magento\Rma\Model\Rma\PermissionChecker | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $permissionCheckerMock;

    /**
     * @var \Magento\Rma\Model\RmaRepository | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaRepositoryMock;

    /**
     * @var \Magento\Rma\Model\Rma | \PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaModelMock;

    /**
     * Sets up the Mocks.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->criteriaBuilderMock = $this->getMockBuilder('Magento\Framework\Service\V1\Data\SearchCriteriaBuilder')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->filterBuilderMock = $this->getMockBuilder('Magento\Framework\Service\V1\Data\FilterBuilder')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->filterMock = $this->getMockBuilder('Magento\Framework\Service\V1\Data\Filter')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->searchCriteriaMock = $this->getMockBuilder('\Magento\Framework\Service\V1\Data\SearchCriteria')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->historyRepositoryMock = $this->getMockBuilder('Magento\Rma\Model\Rma\Status\HistoryRepository')
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $this->commentMock = $this->getMockBuilder('Magento\Rma\Model\Rma\Status\History')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->dataObjectMock = $this->getMockBuilder('Magento\Rma\Service\V1\Data\RmaStatusHistory')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->historyMapperMock = $this->getMockBuilder('Magento\Rma\Service\V1\Data\RmaStatusHistoryMapper')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->searchResultsBuilderMock = $this->getMockBuilder(
            'Magento\Rma\Service\V1\Data\RmaStatusHistorySearchResultsBuilder'
        )
            ->disableOriginalConstructor()
            ->setMethods(['setItems', 'setTotalCount', 'setSearchCriteria', 'create'])
            ->getMock();

        $this->searchResultsMock = $this->getMockBuilder('Magento\Rma\Service\V1\Data\RmaStatusHistorySearchResults')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->rmaRepositoryMock = $this->getMockBuilder('Magento\Rma\Model\RmaRepository')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'get'])
            ->getMock();

        $this->permissionCheckerMock = $this->getMockBuilder('Magento\Rma\Model\Rma\PermissionChecker')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'checkRmaForCustomerContext', 'isCustomerContext'])
            ->getMock();

        $this->rmaModelMock = $this->getMockBuilder('Magento\Rma\Model\Rma')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'getId'])
            ->getMock();

        $this->rmaServiceCommentReadMock = (new ObjectManagerHelper($this))->getObject(
            '\Magento\Rma\Service\V1\CommentRead',
            [
                "historyRepository"    => $this->historyRepositoryMock,
                "historyMapper"        => $this->historyMapperMock,
                "criteriaBuilder"      => $this->criteriaBuilderMock,
                "filterBuilder"        => $this->filterBuilderMock,
                "searchResultsBuilder" => $this->searchResultsBuilderMock,
                "repository"           => $this->rmaRepositoryMock,
                "permissionChecker"    => $this->permissionCheckerMock,
            ]
        );
    }

    /**
     * Test for commentsList method
     *
     * @dataProvider commentsListDataProvider
     *
     * @param int  $id
     * @param bool $isCustomerContext
     *
     * @return void
     */
    public function testCommentsList($id, $isCustomerContext)
    {
        $this->permissionCheckerMock->expects($this->once())->method('checkRmaForCustomerContext');

        $this->permissionCheckerMock
            ->expects($this->once())
            ->method('isCustomerContext')
            ->willReturn($isCustomerContext);

        $this->rmaModelMock->expects($this->once())->method('getId')
            ->willReturn($id);

        $this->rmaRepositoryMock->expects($this->once())->method('get')
            ->with($id)
            ->willReturn($this->rmaModelMock);

        $this->filterBuilderMock
            ->expects($this->at(0))
            ->method('setField')
            ->with($this->equalTo('rma_entity_id'))
            ->willReturn($this->filterBuilderMock);

        $this->filterBuilderMock
            ->expects($this->at(1))
            ->method('setValue')
            ->with($id)
            ->willReturn($this->filterBuilderMock);

        $this->filterBuilderMock
            ->expects($this->at(2))
            ->method('create')
            ->will($this->returnValue($this->filterMock));

        if ($isCustomerContext) {
            $this->filterBuilderMock
                ->expects($this->at(3))
                ->method('setField')
                ->with($this->equalTo('is_visible_on_front'))
                ->willReturn($this->filterBuilderMock);

            $this->filterBuilderMock
                ->expects($this->at(4))
                ->method('setValue')
                ->with($id)
                ->willReturn($this->filterBuilderMock);

            $this->filterBuilderMock
                ->expects($this->at(5))
                ->method('create')
                ->will($this->returnValue($this->filterMock));

            $this->criteriaBuilderMock
                ->expects($this->once())
                ->method('addFilter')
                ->with($this->equalTo([$this->filterMock, $this->filterMock]));
        } else {
            $this->criteriaBuilderMock
                ->expects($this->once())
                ->method('addFilter')
                ->with($this->equalTo([$this->filterMock]));
        }

        $this->criteriaBuilderMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->searchCriteriaMock));

        $this->historyRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo($this->searchCriteriaMock))
            ->will($this->returnValue([$this->commentMock]));

        $this->historyMapperMock
            ->expects($this->once())
            ->method('extractDto')
            ->with($this->equalTo($this->commentMock))
            ->will($this->returnValue($this->dataObjectMock));

        $this->searchResultsBuilderMock
            ->expects($this->once())
            ->method('setItems')
            ->with([$this->dataObjectMock])
            ->willReturnSelf();

        $this->searchResultsBuilderMock
            ->expects($this->once())
            ->method('setTotalCount')
            ->with(1)
            ->willReturnSelf();

        $this->searchResultsBuilderMock
            ->expects($this->once())
            ->method('setSearchCriteria')
            ->with($this->equalTo($this->searchCriteriaMock))
            ->willReturnSelf();

        $this->searchResultsBuilderMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($this->searchResultsMock);

        $this->assertEquals(
            $this->searchResultsMock,
            $this->rmaServiceCommentReadMock->commentsList($id)
        );
    }

    /**
     *
     */
    public function commentsListDataProvider()
    {
        return [
            1 => [42, false],
            2 => [1, true],
        ];
    }
}
