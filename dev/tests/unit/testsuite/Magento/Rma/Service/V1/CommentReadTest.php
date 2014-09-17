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
            ->setMethods(['setItems', 'setTotalCount', 'create'])
            ->getMock();

        $this->searchResultsMock = $this->getMockBuilder('Magento\Rma\Service\V1\Data\RmaStatusHistorySearchResults')
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->rmaServiceCommentReadMock = (new ObjectManagerHelper($this))->getObject(
            '\Magento\Rma\Service\V1\CommentRead',
            [
                "historyRepository" => $this->historyRepositoryMock,
                "historyMapper" => $this->historyMapperMock,
                "criteriaBuilder" => $this->criteriaBuilderMock,
                "filterBuilder" => $this->filterBuilderMock,
                "searchResultsBuilder" => $this->searchResultsBuilderMock,
            ]
        );
    }

    /**
     * Test for commentsList method
     *
     * @return void
     */
    public function testCommentsList()
    {
        $id = 1;

        $this->filterBuilderMock
            ->expects($this->once())
            ->method('setField')
            ->with($this->equalTo('rma_entity_id'))
            ->willReturnSelf();

        $this->filterBuilderMock
            ->expects($this->once())
            ->method('setValue')
            ->with($this->equalTo($id))
            ->willReturnSelf();

        $this->filterBuilderMock
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->filterMock));

        $this->criteriaBuilderMock
            ->expects($this->once())
            ->method('addFilter')
            ->with($this->equalTo(['eq' => $this->filterMock]));

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
            ->method('create')
            ->willReturn($this->searchResultsMock);

        $this->assertEquals(
            $this->searchResultsMock,
            $this->rmaServiceCommentReadMock->commentsList($id)
        );
    }
}
