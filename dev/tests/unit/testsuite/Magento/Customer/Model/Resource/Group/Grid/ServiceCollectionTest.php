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

use Magento\Customer\Service\V1\Data\SearchCriteria;

class ServiceCollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\TestFramework\Helper\ObjectManager */
    protected $objectManager;

    /** @var \Magento\Customer\Service\V1\Data\FilterBuilder */
    protected $filterBuilder;

    /** @var \Magento\Customer\Service\V1\Data\SearchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /** @var \Magento\Customer\Service\V1\Data\SearchResults */
    protected $searchResults;

    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Customer\Service\V1\CustomerGroupServiceInterface */
    protected $groupServiceMock;

    /** @var ServiceCollection */
    protected $serviceCollection;

    public function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->filterBuilder = new \Magento\Customer\Service\V1\Data\FilterBuilder();
        $this->searchCriteriaBuilder = new \Magento\Customer\Service\V1\Data\SearchCriteriaBuilder();
        $this->groupServiceMock = $this->getMockBuilder('\Magento\Customer\Service\V1\CustomerGroupServiceInterface')
            ->getMock();
        $this->searchResults = (new \Magento\Customer\Service\V1\Data\SearchResultsBuilder())->create();

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

    public function testGetSearchCriteriaImplicitEq()
    {
        /** @var SearchCriteria $expectedSearchCriteria */
        $expectedSearchCriteria = $this->searchCriteriaBuilder
            ->setCurrentPage(1)
            ->setPageSize(0)
            ->addSortOrder('name', SearchCriteria::SORT_ASC)
            ->addFilter($this->filterBuilder->setField('name')->setConditionType('eq')->setValue('Magento')->create())
            ->create();

        // Verifies that the search criteria Data Object created by the serviceCollection matches expected
        $this->groupServiceMock->expects($this->once())
            ->method('searchGroups')
            ->with($this->equalTo($expectedSearchCriteria))
            ->will($this->returnValue($this->searchResults));

        // Now call service collection to load the data.  This causes it to create the search criteria Data Object
        $this->serviceCollection->addFieldToFilter('name', 'Magento');
        $this->serviceCollection->setOrder('name', ServiceCollection::SORT_ORDER_ASC);
        $this->serviceCollection->loadData();
    }

    public function testGetSearchCriteriaOneField()
    {
        $field = 'age';
        $conditionType = 'gt';
        $value = '35';

        /** @var SearchCriteria $expectedSearchCriteria */
        $expectedSearchCriteria = $this->searchCriteriaBuilder
            ->setCurrentPage(1)
            ->setPageSize(0)
            ->addSortOrder('name', SearchCriteria::SORT_ASC)
            ->addFilter(
                $this->filterBuilder->setField($field)->setConditionType($conditionType)->setValue($value)->create()
            )
            ->create();

        // Verifies that the search criteria Data Object created by the serviceCollection matches expected
        $this->groupServiceMock->expects($this->once())
            ->method('searchGroups')
            ->with($this->equalTo($expectedSearchCriteria))
            ->will($this->returnValue($this->searchResults));

        // Now call service collection to load the data.  This causes it to create the search criteria Data Object
        $this->serviceCollection->addFieldToFilter($field, [$conditionType => $value]);
        $this->serviceCollection->setOrder('name', ServiceCollection::SORT_ORDER_ASC);
        $this->serviceCollection->loadData();
    }

    public function testGetSearchCriteriaOr()
    {
        // Test ((A == 1) or (B == 1 ))
        $fieldA = 'A';
        $fieldB = 'B';
        $value = 1;

        /** @var SearchCriteria $expectedSearchCriteria */
        $expectedSearchCriteria = $this->searchCriteriaBuilder
            ->setCurrentPage(1)
            ->setPageSize(0)
            ->addSortOrder('name', SearchCriteria::SORT_ASC)
            ->addOrGroup(
                [
                    $this->filterBuilder->setField($fieldA)->setConditionType('eq')->setValue($value)->create(),
                    $this->filterBuilder->setField($fieldB)->setConditionType('eq')->setValue($value)->create(),
                ]
            )
            ->create();

        // Verifies that the search criteria Data Object created by the serviceCollection matches expected
        $this->groupServiceMock->expects($this->once())
            ->method('searchGroups')
            ->with($this->equalTo($expectedSearchCriteria))
            ->will($this->returnValue($this->searchResults));

        // Now call service collection to load the data.  This causes it to create the search criteria Data Object
        $this->serviceCollection->addFieldToFilter([$fieldA, $fieldB], [$value, $value]);
        $this->serviceCollection->setOrder('name', ServiceCollection::SORT_ORDER_ASC);
        $this->serviceCollection->loadData();
    }

    public function testGetSearchCriteriaAnd()
    {
        // Test ((A > 1) and (B > 1))
        $fieldA = 'A';
        $fieldB = 'B';
        $value = 1;

        /** @var SearchCriteria $expectedSearchCriteria */
        $expectedSearchCriteria = $this->searchCriteriaBuilder
            ->setCurrentPage(1)
            ->setPageSize(0)
            ->addSortOrder('name', SearchCriteria::SORT_ASC)
            ->addFilter($this->filterBuilder->setField($fieldA)->setConditionType('gt')->setValue($value)->create())
            ->addFilter($this->filterBuilder->setField($fieldB)->setConditionType('gt')->setValue($value)->create())
            ->create();

        // Verifies that the search criteria Data Object created by the serviceCollection matches expected
        $this->groupServiceMock->expects($this->once())
            ->method('searchGroups')
            ->with($this->equalTo($expectedSearchCriteria))
            ->will($this->returnValue($this->searchResults));

        // Now call service collection to load the data.  This causes it to create the search criteria Data Object
        $this->serviceCollection->addFieldToFilter($fieldA, ['gt' => $value]);
        $this->serviceCollection->addFieldToFilter($fieldB, ['gt' => $value]);
        $this->serviceCollection->setOrder('name', ServiceCollection::SORT_ORDER_ASC);
        $this->serviceCollection->loadData();
    }

    /**
     * @param string[] $fields
     * @param array $conditions
     *
     * @expectedException \Magento\Exception
     * @expectedExceptionMessage When passing in a field array there must be a matching condition array
     * @dataProvider addFieldToFilterInconsistentArraysDataProvider
     */
    public function testAddFieldToFilterInconsistentArrays($fields, $conditions)
    {
        $this->serviceCollection->addFieldToFilter($fields, $conditions);
    }

    public function addFieldToFilterInconsistentArraysDataProvider()
    {
        return [
            'missingCondition' => [
                ['fieldA', 'missingCondition'],
                [['eq' => 'A']]
            ],
            'missingField' => [
                ['fieldA'],
                [['eq' => 'A'], ['eq' => 'B']]
            ],
        ];
    }
}
