<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\SalesArchive\Service\V1;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\SalesArchive\Service\V1\WriteService */
    protected $writeService;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $orderRepositoryMock;

    /** @var \Magento\SalesArchive\Service\V1\Data\ArchiveMapper|\PHPUnit_Framework_MockObject_MockObject */
    protected $archiveMapperMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $archiveSearchResultsBuilderMock;

    /** @var \Magento\Framework\Api\SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject */
    protected $searchCriteriaBuilderMock;

    /** @var \Magento\Framework\Api\FilterBuilder|\PHPUnit_Framework_MockObject_MockObject */
    protected $filterBuilderMock;

    /** @var \Magento\SalesArchive\Model\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $configMock;

    /** @var \Magento\Framework\Stdlib\DateTime|\PHPUnit_Framework_MockObject_MockObject */
    protected $dateTimeMock;

    /**
     * @var \Magento\SalesArchive\Model\Archive|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $archiveMock;

    protected function setUp()
    {
        $this->orderRepositoryMock = $this->getMock(
            'Magento\Sales\Model\OrderRepository',
            ['find'],
            [],
            '',
            false
        );
        $this->archiveMapperMock = $this->getMock(
            'Magento\SalesArchive\Service\V1\Data\ArchiveMapper',
            [],
            [],
            '',
            false
        );
        $this->archiveSearchResultsBuilderMock = $this->getMock(
            'Magento\SalesArchive\Service\V1\Data\ArchiveSearchResultsBuilder',
            ['create', 'setItems', 'setTotalCount', 'setSearchCriteria'],
            [],
            '',
            false
        );
        $this->searchCriteriaBuilderMock = $this->getMock(
            'Magento\Framework\Api\SearchCriteriaBuilder',
            [],
            [],
            '',
            false
        );
        $this->filterBuilderMock = $this->getMock('Magento\Framework\Api\FilterBuilder', [], [], '', false);
        $this->configMock = $this->getMock('Magento\SalesArchive\Model\Config', [], [], '', false);
        $this->dateTimeMock = $this->getMock('Magento\Framework\Stdlib\DateTime');
        $this->archiveMock = $this->getMock('Magento\SalesArchive\Model\Archive', [], [], '', false);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->writeService = $this->objectManagerHelper->getObject(
            'Magento\SalesArchive\Service\V1\WriteService',
            [
                'archive' => $this->archiveMock,
                'orderRepository' => $this->orderRepositoryMock,
                'archiveMapper' => $this->archiveMapperMock,
                'searchResultsBuilder' => $this->archiveSearchResultsBuilderMock,
                'criteriaBuilder' => $this->searchCriteriaBuilderMock,
                'filterBuilder' => $this->filterBuilderMock,
                'salesArchiveConfig' => $this->configMock,
                'dateTime' => $this->dateTimeMock
            ]
        );
    }

    public function testInvokeEmptyStatuses()
    {
        $statuses = [];
        $serviceResultsMock = $this->getMockBuilder('Magento\Framework\Api\SearchResults')
            ->disableOriginalConstructor()->setMethods([])->getMock();
        $searchCriteriaMock = $this->getMockBuilder('Magento\Framework\Api\SearchCriteria')
            ->disableOriginalConstructor()->setMethods([])->getMock();

        $this->configMock->expects($this->once())->method('getArchiveOrderStatuses')->will(
            $this->returnValue($statuses)
        );
        $this->archiveSearchResultsBuilderMock->expects($this->once())->method('create')->will(
            $this->returnValue($serviceResultsMock)
        );

        $this->assertSame($serviceResultsMock, $this->writeService->getList($searchCriteriaMock));
    }

    public function testInvokeEmptyArchiveAge()
    {
        list($statuses, $archiveAge) = [['status'], 0];
        $serviceResultsMock = $this->getMockBuilder('Magento\Framework\Api\SearchResults')
            ->disableOriginalConstructor()->setMethods([])->getMock();
        $filterMock = $this->getMockBuilder('Magento\Framework\Api\Filter')
            ->disableOriginalConstructor()->setMethods([])->getMock();
        $searchFilterMock = $this->getMockBuilder('Magento\Framework\Api\Filter')
            ->disableOriginalConstructor()->setMethods([])->getMock();
        $filterGroupMock = $this->getMockBuilder('Magento\Framework\Api\Search\FilterGroup')
            ->disableOriginalConstructor()->setMethods([])->getMock();
        $searchCriteriaMock = $this->getMockBuilder('Magento\Framework\Api\SearchCriteria')
            ->disableOriginalConstructor()->setMethods([])->getMock();
        $searchCriteriaBuildMock = $this->getMockBuilder('Magento\Framework\Api\SearchCriteria')
            ->disableOriginalConstructor()->setMethods([])->getMock();
        $orderMock = $this->getMockBuilder('Magento\Sales\Model\Order')->disableOriginalConstructor()->setMethods([])
            ->getMock();
        $archiveDtoMock = $this->getMockBuilder('Magento\SalesArchive\Service\V1\Data\Archive')
            ->disableOriginalConstructor()->setMethods([])->getMock();

        $this->configMock->expects($this->once())->method('getArchiveOrderStatuses')->will(
            $this->returnValue($statuses)
        );
        $this->filterBuilderMock->expects($this->at(0))->method('setField')->with('status')->will($this->returnSelf());
        $this->filterBuilderMock->expects($this->at(1))->method('setValue')->with($statuses)->will($this->returnSelf());
        $this->filterBuilderMock->expects($this->at(2))->method('setConditionType')
            ->with('in')->will($this->returnSelf());
        $this->filterBuilderMock->expects($this->at(3))->method('create')->will($this->returnValue($filterMock));

        $this->configMock->expects($this->once())->method('getArchiveAge')->will(
            $this->returnValue($archiveAge)
        );
        $this->searchCriteriaBuilderMock->expects($this->at(0))->method('addFilter')->with([$filterMock]);
        $searchCriteriaMock->expects($this->once())->method('getFilterGroups')->will(
            $this->returnValue([$filterGroupMock])
        );
        $filterGroupMock->expects($this->once())->method('getFilters')->will($this->returnValue([$searchFilterMock]));
        $this->searchCriteriaBuilderMock->expects($this->at(1))->method('addFilter')->with([$searchFilterMock]);
        $this->searchCriteriaBuilderMock->expects($this->once())->method('create')->will(
            $this->returnValue($searchCriteriaBuildMock)
        );
        $this->orderRepositoryMock->expects($this->once())->method('find')->with($searchCriteriaBuildMock)
            ->will($this->returnValue([$orderMock]));
        $this->archiveMapperMock->expects($this->once())->method('extractDto')->with($orderMock)
            ->will($this->returnValue($archiveDtoMock));

        $this->archiveSearchResultsBuilderMock->expects($this->once())->method('setItems')
            ->with([$archiveDtoMock])->will($this->returnSelf());
        $this->archiveSearchResultsBuilderMock->expects($this->once())->method('setTotalCount')
            ->with(1)->will($this->returnSelf());
        $this->archiveSearchResultsBuilderMock->expects($this->once())->method('setSearchCriteria')
            ->with($searchCriteriaBuildMock)->will($this->returnSelf());

        $this->archiveSearchResultsBuilderMock->expects($this->once())->method('create')->will(
            $this->returnValue($serviceResultsMock)
        );

        $this->assertSame($serviceResultsMock, $this->writeService->getList($searchCriteriaMock));
    }

    /**
     * @dataProvider moveOrdersToArchiveDataProvider
     */
    public function testMoveOrdersToArchive($value)
    {
        $this->archiveMock->expects($this->any())
            ->method('archiveOrders')
            ->will($this->returnValue($value));
        $this->assertEquals($value, $this->writeService->moveOrdersToArchive());
    }

    /**
     * @return array
     */
    public function moveOrdersToArchiveDataProvider()
    {
        return [
            [true],
            [false]
        ];
    }

    /**
     * @param $id
     * @param $value
     * @dataProvider removeOrderFromArchiveByIdDataProvider
     */
    public function testRemoveOrderFromArchiveById($id, $value)
    {
        $this->archiveMock->expects($this->any())
            ->method('removeOrdersFromArchiveById')
            ->with($id)
            ->will($this->returnValue($value));
        $this->assertEquals($value, $this->writeService->removeOrderFromArchiveById($id));
    }

    /**
     * @return array
     */
    public function removeOrderFromArchiveByIdDataProvider()
    {
        return [
            [1, true],
            [2, false]
        ];
    }
}
