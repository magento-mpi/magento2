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

use Magento\Customer\Service\V1;

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
        $groups = $this->_groupService->getGroups(FALSE);
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
        $group = $this->_groupService->getGroup($testGroup['id']);
        $this->assertEquals($testGroup['id'], $group->getId());
        $this->assertEquals($testGroup['code'], $group->getCode());
        $this->assertEquals($testGroup['tax_class_id'], $group->getTaxClassId());
    }

    /**
     * @return array
     */
    public function getGroupsDataProvider()
    {
        return [ [['id' => 0, 'code' => 'NOT LOGGED IN', 'tax_class_id' => 3]],
            [['id' => 1, 'code' => 'General', 'tax_class_id' => 3]],
            [['id' => 2, 'code' => 'Wholesale', 'tax_class_id' => 3]],
            [['id' => 3, 'code' => 'Retailer', 'tax_class_id' => 3]],
        ];
    }

    public function testCreateGroup()
    {
        $group = new V1\Dto\CustomerGroup([
          'id' => null,
          'code' => 'Test Group',
          'tax_class_id' => 4
        ]);
        $groupId = $this->_groupService->saveGroup($group);
        $this->assertNotNull($groupId);

        $newGroup = $this->_groupService->getGroup($groupId);
        $this->assertEquals($groupId, $newGroup->getId());
        $this->assertEquals($group->getCode(), $newGroup->getCode());
        $this->assertEquals($group->getTaxClassId(), $newGroup->getTaxClassId());
    }

    public function testUpdateGroup()
    {
        $group = new V1\Dto\CustomerGroup([
          'id' => null,
          'code' => 'New Group',
          'tax_class_id' => 4
        ]);
        $groupId = $this->_groupService->saveGroup($group);
        $this->assertNotNull($groupId);

        $newGroup = $this->_groupService->getGroup($groupId);
        $this->assertEquals($groupId, $newGroup->getId());
        $this->assertEquals($group->getCode(), $newGroup->getCode());
        $this->assertEquals($group->getTaxClassId(), $newGroup->getTaxClassId());

        $updates = new V1\Dto\CustomerGroup([
          'id' => $groupId,
          'code' => 'Updated Group',
          'tax_class_id' => 2
        ]);
        $newId = $this->_groupService->saveGroup($updates);
        $this->assertEquals($newId, $groupId);
        $updatedGroup = $this->_groupService->getGroup($groupId);
        $this->assertEquals($updates->getCode(), $updatedGroup->getCode());
        $this->assertEquals($updates->getTaxClassId(), $updatedGroup->getTaxClassId());
    }

    /**
     * @param Dto\Filter[] $filters
     * @param Dto\Filter[] $orGroup
     * @param array $expectedResult array of expected results indexed by ID
     *
     * @dataProvider searchGroupsDataProvider
     */
    public function testSearchGroups($filters, $orGroup, $expectedResult)
    {
        $searchBuilder = new V1\Dto\SearchCriteriaBuilder();
        foreach ($filters as $filter) {
            $searchBuilder->addFilter($filter);
        }
        if (!is_null($orGroup)) {
            $searchBuilder->addOrGroup($orGroup);
        }

        $searchResults = $this->_groupService->searchGroups($searchBuilder->create());

        /** @var $item Dto\CustomerGroup*/
        foreach ($searchResults->getItems() as $item) {
            $this->assertEquals($expectedResult[$item->getId()]['code'], $item->getCode());
            $this->assertEquals($expectedResult[$item->getId()]['tax_class_id'], $item->getTaxClassId());
            unset($expectedResult[$item->getId()]);
        }
    }

    public function searchGroupsDataProvider()
    {
        return [
            'eq' => [
                [(new V1\Dto\FilterBuilder())->setField('code')->setValue('General')->create()],
                null,
                [1 => ['code' => 'General', 'tax_class_id' => 3]]
            ],
            'and' => [
                [
                    (new V1\Dto\FilterBuilder())->setField('code')->setValue('General')->create(),
                    (new V1\Dto\FilterBuilder())->setField('tax_class_id')->setValue('3')->create(),
                    (new V1\Dto\FilterBuilder())->setField('id')->setValue('1')->create(),
                ],
                [],
                [1 => ['code' => 'General', 'tax_class_id' => 3]]
            ],
            'or' => [
                [],
                [
                    (new V1\Dto\FilterBuilder())->setField('code')->setValue('General')->create(),
                    (new V1\Dto\FilterBuilder())->setField('code')->setValue('Wholesale')->create(),
                ],
                [
                    1 => ['code' => 'General', 'tax_class_id' => 3],
                    2 => ['code' => 'Wholesale', 'tax_class_id' => 3]
                ]
            ],
            'like' => [
                [(new V1\Dto\FilterBuilder())->setField('code')->setValue('er')->setConditionType('like')->create()],
                [],
                [
                    1 => ['code' => 'General', 'tax_class_id' => 3],
                    3 => ['code' => 'Retailer', 'tax_class_id' => 3]
                ]
            ],
        ];
    }
}
