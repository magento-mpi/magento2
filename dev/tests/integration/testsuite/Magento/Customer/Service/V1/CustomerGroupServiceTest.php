<?php
/**
 * Integration test for service layer \Magento\Customer\Service\Customer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

use Magento\Customer\Service\V1\Data\CustomerGroup;
use Magento\Service\V1\Data\FilterBuilder;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Service\V1\Data\Filter;

class CustomerGroupServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager = null;

    /**
     * @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface
     */
    protected $_groupService = null;

    protected function setUp()
    {

        $this->_objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_groupService = $this->_objectManager->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');
    }

    protected function tearDown()
    {
        $this->_objectManager = null;
        $this->_groupService = null;
    }

    /**
     * Cleaning up the extra groups that might have been created as part of the testing.
     */
    public static function tearDownAfterClass()
    {
        /** @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface $customerGroupService */
        $customerGroupService = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Customer\Service\V1\CustomerGroupServiceInterface'
        );
        foreach ($customerGroupService->getGroups() as $group) {
            if ($group->getId() > 3) {
                $customerGroupService->deleteGroup($group->getId());
            }
        }
    }

    /**
     */
    public function testGetGroups()
    {
        $groups = $this->_groupService->getGroups();
        $this->assertEquals(4, count($groups));
        $this->assertEquals(
            [0, 'NOT LOGGED IN', 3],
            [$groups[0]->getId(), $groups[0]->getCode(), $groups[0]->getTaxClassId()]
        );
        $this->assertEquals(
            [1, 'General', 3],
            [$groups[1]->getId(), $groups[1]->getCode(), $groups[1]->getTaxClassId()]
        );
        $this->assertEquals(
            [2, 'Wholesale', 3],
            [$groups[2]->getId(), $groups[2]->getCode(), $groups[2]->getTaxClassId()]
        );
        $this->assertEquals(
            [3, 'Retailer', 3],
            [$groups[3]->getId(), $groups[3]->getCode(), $groups[3]->getTaxClassId()]
        );
    }

    /**
     */
    public function testGetGroupsFiltered()
    {
        $groups = $this->_groupService->getGroups(false);
        $this->assertEquals(3, count($groups));
        $this->assertEquals(
            [1, 'General', 3],
            [$groups[0]->getId(), $groups[0]->getCode(), $groups[0]->getTaxClassId()]
        );
        $this->assertEquals(
            [2, 'Wholesale', 3],
            [$groups[1]->getId(), $groups[1]->getCode(), $groups[1]->getTaxClassId()]
        );
        $this->assertEquals(
            [3, 'Retailer', 3],
            [$groups[2]->getId(), $groups[2]->getCode(), $groups[2]->getTaxClassId()]
        );
    }

    /**
     * @param $testGroup
     * @dataProvider getGroupsDataProvider
     */
    public function testGetGroup($testGroup)
    {
        $group = $this->_groupService->getGroup($testGroup[CustomerGroup::ID]);
        $this->assertEquals($testGroup[CustomerGroup::ID], $group->getId());
        $this->assertEquals($testGroup[CustomerGroup::CODE], $group->getCode());
        $this->assertEquals($testGroup[CustomerGroup::TAX_CLASS_ID], $group->getTaxClassId());
    }

    /**
     * @return array
     */
    public function getGroupsDataProvider()
    {
        return [ [[CustomerGroup::ID => 0, CustomerGroup::CODE => 'NOT LOGGED IN', CustomerGroup::TAX_CLASS_ID => 3]],
            [[CustomerGroup::ID => 1, CustomerGroup::CODE => 'General', CustomerGroup::TAX_CLASS_ID => 3]],
            [[CustomerGroup::ID => 2, CustomerGroup::CODE => 'Wholesale', CustomerGroup::TAX_CLASS_ID => 3]],
            [[CustomerGroup::ID => 3, CustomerGroup::CODE => 'Retailer', CustomerGroup::TAX_CLASS_ID => 3]],
        ];
    }

    public function testCreateGroup()
    {
        $group = (new Data\CustomerGroupBuilder())->setId(null)->setCode('Test Group')->setTaxClassId(3)->create();
        $groupId = $this->_groupService->saveGroup($group);
        $this->assertNotNull($groupId);

        $newGroup = $this->_groupService->getGroup($groupId);
        $this->assertEquals($groupId, $newGroup->getId());
        $this->assertEquals($group->getCode(), $newGroup->getCode());
        $this->assertEquals($group->getTaxClassId(), $newGroup->getTaxClassId());
    }

    public function testUpdateGroup()
    {
        $group = (new Data\CustomerGroupBuilder())->setId(null)->setCode('New Group')->setTaxClassId(3)->create();
        $groupId = $this->_groupService->saveGroup($group);
        $this->assertNotNull($groupId);

        $newGroup = $this->_groupService->getGroup($groupId);
        $this->assertEquals($groupId, $newGroup->getId());
        $this->assertEquals($group->getCode(), $newGroup->getCode());
        $this->assertEquals($group->getTaxClassId(), $newGroup->getTaxClassId());

        $updates = (new Data\CustomerGroupBuilder())->setId($groupId)->setCode('Updated Group')->setTaxClassId(3)
            ->create();
        $newId = $this->_groupService->saveGroup($updates);
        $this->assertEquals($newId, $groupId);
        $updatedGroup = $this->_groupService->getGroup($groupId);
        $this->assertEquals($updates->getCode(), $updatedGroup->getCode());
        $this->assertEquals($updates->getTaxClassId(), $updatedGroup->getTaxClassId());
    }

    /**
     * @param Filter[] $filters
     * @param Filter[] $filterGroup
     * @param array $expectedResult array of expected results indexed by ID
     *
     * @dataProvider searchGroupsDataProvider
     */
    public function testSearchGroups($filters, $filterGroup, $expectedResult)
    {
        /** @var \Magento\Service\V1\Data\SearchCriteriaBuilder $searchBuilder */
        $searchBuilder = Bootstrap::getObjectManager()
            ->create('Magento\Service\V1\Data\SearchCriteriaBuilder');
        foreach ($filters as $filter) {
            $searchBuilder->addFilter([$filter]);
        }
        if (!is_null($filterGroup)) {
            $searchBuilder->addFilter($filterGroup);
        }

        $searchResults = $this->_groupService->searchGroups($searchBuilder->create());

        /** @var $item Data\CustomerGroup */
        foreach ($searchResults->getItems() as $item) {
            $this->assertEquals($expectedResult[$item->getId()][CustomerGroup::CODE], $item->getCode());
            $this->assertEquals($expectedResult[$item->getId()][CustomerGroup::TAX_CLASS_ID], $item->getTaxClassId());
            unset($expectedResult[$item->getId()]);
        }
    }

    public function searchGroupsDataProvider()
    {
        return [
            'eq' => [
                [(new FilterBuilder())->setField(CustomerGroup::CODE)->setValue('General')->create()],
                null,
                [1 => [CustomerGroup::CODE => 'General', CustomerGroup::TAX_CLASS_ID => 3]]
            ],
            'and' => [
                [
                    (new FilterBuilder())->setField(CustomerGroup::CODE)->setValue('General')->create(),
                    (new FilterBuilder())->setField(CustomerGroup::TAX_CLASS_ID)->setValue('3')->create(),
                    (new FilterBuilder())->setField(CustomerGroup::ID)->setValue('1')->create(),
                ],
                [],
                [1 => [CustomerGroup::CODE => 'General', CustomerGroup::TAX_CLASS_ID => 3]]
            ],
            'or' => [
                [],
                [
                    (new FilterBuilder())->setField(CustomerGroup::CODE)->setValue('General')->create(),
                    (new FilterBuilder())->setField(CustomerGroup::CODE)->setValue('Wholesale')->create(),
                ],
                [
                    1 => [CustomerGroup::CODE => 'General', CustomerGroup::TAX_CLASS_ID => 3],
                    2 => [CustomerGroup::CODE => 'Wholesale', CustomerGroup::TAX_CLASS_ID => 3]
                ]
            ],
            'like' => [
                [
                    (new FilterBuilder())->setField(CustomerGroup::CODE)->setValue('er')->setConditionType('like')
                        ->create()
                ],
                [],
                [
                    1 => [CustomerGroup::CODE => 'General', CustomerGroup::TAX_CLASS_ID => 3],
                    3 => [CustomerGroup::CODE => 'Retailer', CustomerGroup::TAX_CLASS_ID => 3]
                ]
            ],
        ];
    }
}
