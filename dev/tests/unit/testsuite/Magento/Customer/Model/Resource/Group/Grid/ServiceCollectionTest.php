<?php
/**
 * Unit test for \Magento\Customer\Model\Resource\Group\Grid\ServiceCollection
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Resource\Group\Grid;

use Magento\Customer\Service\V1\Dto\SearchCriteria;

class ServiceCollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\TestFramework\Helper\ObjectManager */
    protected $objectManager;

    /** @var \Magento\Customer\Service\V1\Dto\FilterBuilder */
    protected $filterBuilder;

    /** @var \Magento\Customer\Service\V1\Dto\SearchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /** @var \Magento\Customer\Service\V1\Dto\SearchResults */
    protected $searchResults;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Service\V1\CustomerGroupServiceInterface */
    protected $groupServiceMock;

    /** @var ServiceCollection */
    protected $serviceCollection;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->filterBuilder = new \Magento\Customer\Service\V1\Dto\FilterBuilder();
        $this->searchCriteriaBuilder = new \Magento\Customer\Service\V1\Dto\SearchCriteriaBuilder();
        $this->groupServiceMock = $this->getMockBuilder('\Magento\Customer\Service\V1\CustomerGroupServiceInterface')
            ->getMock();
        $this->searchResults = new \Magento\Customer\Service\V1\Dto\SearchResults([]);

        $this->serviceCollection = $this->objectManager
            ->getObject(
                'Magento\Customer\Model\Resource\Group\Grid\ServiceCollection',
                [
                'filterBuilder' => $this->filterBuilder,
                'searchCriteriaBuilder' => $this->searchCriteriaBuilder,
                'groupService' => $this->groupServiceMock,
                ]
            );
    }

    /**
     * @param string|int|array $field
     * @param string|int|array $name
     * @param array $expectedFilters
     *
     * @dataProvider getSearchCriteriaDataProvider
     */
    public function testGetSearchCriteria($field, $condition, $expectedFilters)
    {
        // Setup the expected search criteria
        foreach ($expectedFilters as $expectedFilter) {
            $this->searchCriteriaBuilder->addFilter($expectedFilter);
        }

        $customerGroupBuilder = new \Magento\Customer\Service\V1\Dto\CustomerGroupBuilder();
        $customerGroup = $customerGroupBuilder->setCode('code')->setId('1')->create();

        $searchResultsBuilder = new \Magento\Customer\Service\V1\Dto\SearchResultsBuilder();
        $this->searchResults = $searchResultsBuilder->setItems([$customerGroup])->setTotalCount(1)->create();
        $expectedSearchCriteria = $this->searchCriteriaBuilder
            ->setCurrentPage(1)
            ->setPageSize(0)
            ->addSortOrder('name', SearchCriteria::SORT_ASC)
            ->create();

        // Verifies that the search criteria DTO created by the serviceCollection matches expected
        $this->groupServiceMock->expects($this->once())
            ->method('searchGroups')
            ->with($this->equalTo($expectedSearchCriteria))
            ->will($this->returnValue($this->searchResults));

        // Now call service collection to load the data.  This causes it to create the search criteria DTO
        $this->serviceCollection->addFieldToFilter($field, $condition);
        $this->serviceCollection->setOrder('name', ServiceCollection::SORT_ORDER_ASC);
        $this->serviceCollection->loadData();
    }

    public function getSearchCriteriaDataProvider()
    {
        $filterBuilder = new \Magento\Customer\Service\V1\Dto\FilterBuilder();
        return [
            [
                'name',
                ['like' => 'Mage'],
                [$filterBuilder->setField('name')->setConditionType('like')->setValue('Mage')->create()],
            ],
            [
                'name',
                'Magento',
                [$filterBuilder->setField('name')->setConditionType('eq')->setValue('Magento')->create()],
            ],
            [
                'age',
                ['gt' => 35],
                [$filterBuilder->setField('age')->setConditionType('gt')->setValue(35)->create()],
            ],
            [
                ['city', 'age'],
                ['Austin', ['gt' => 35] ],
                [
                    $filterBuilder->setField('city')->setConditionType('eq')->setValue('Austin')->create(),
                    $filterBuilder->setField('age')->setConditionType('gt')->setValue(35)->create(),
                ],
            ]
        ];
    }

    /**
     * @expectedException \Magento\Exception
     * @expectedExceptionMessage When passing in a field array there must be a matching condition array.
     */
    public function testAddFieldToFilterNoMatchException()
    {
        $this->serviceCollection->addFieldToFilter(['city', 'age'], ['Austin', ['gt' => 35], 'Male']);
    }

}
