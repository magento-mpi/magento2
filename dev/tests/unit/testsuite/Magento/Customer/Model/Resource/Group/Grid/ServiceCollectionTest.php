<?php
/**
 * Unit test for converter \Magento\Customer\Model\Resource\Group\Grid\ServiceCollection
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
        // \Magento\Customer\Service\V1\Dto\FilterBuilder
        foreach ($expectedFilters as $expectedFilter) {
            $this->searchCriteriaBuilder->addFilter($expectedFilter);
        }

        $expectedSearchCriteria = $this->searchCriteriaBuilder->setCurrentPage(1)->setPageSize(0)->create();

        $this->groupServiceMock->expects($this->once()) // exactly(count($expectedFilters)))
            ->method('searchGroups')
            ->with($this->equalTo($expectedSearchCriteria))
            ->will($this->returnValue($this->searchResults));
        $this->serviceCollection->addFieldToFilter($field, $condition);
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
}
