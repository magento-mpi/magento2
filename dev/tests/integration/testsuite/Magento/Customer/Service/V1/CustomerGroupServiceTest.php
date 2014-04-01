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

use Magento\Customer\Model\Group;
use Magento\TestFramework\Helper\Bootstrap;

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

        $this->_objectManager = Bootstrap::getObjectManager();
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
        $customerGroupService = Bootstrap::getObjectManager()->get(
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
            array(0, 'NOT LOGGED IN', 3),
            array($groups[0]->getId(), $groups[0]->getCode(), $groups[0]->getTaxClassId())
        );
        $this->assertEquals(
            array(1, 'General', 3),
            array($groups[1]->getId(), $groups[1]->getCode(), $groups[1]->getTaxClassId())
        );
        $this->assertEquals(
            array(2, 'Wholesale', 3),
            array($groups[2]->getId(), $groups[2]->getCode(), $groups[2]->getTaxClassId())
        );
        $this->assertEquals(
            array(3, 'Retailer', 3),
            array($groups[3]->getId(), $groups[3]->getCode(), $groups[3]->getTaxClassId())
        );
    }

    /**
     */
    public function testGetGroupsFiltered()
    {
        $groups = $this->_groupService->getGroups(false);
        $this->assertEquals(3, count($groups));
        $this->assertEquals(
            array(1, 'General', 3),
            array($groups[0]->getId(), $groups[0]->getCode(), $groups[0]->getTaxClassId())
        );
        $this->assertEquals(
            array(2, 'Wholesale', 3),
            array($groups[1]->getId(), $groups[1]->getCode(), $groups[1]->getTaxClassId())
        );
        $this->assertEquals(
            array(3, 'Retailer', 3),
            array($groups[2]->getId(), $groups[2]->getCode(), $groups[2]->getTaxClassId())
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
        return array(
            array(array('id' => 0, 'code' => 'NOT LOGGED IN', 'tax_class_id' => 3)),
            array(array('id' => 1, 'code' => 'General', 'tax_class_id' => 3)),
            array(array('id' => 2, 'code' => 'Wholesale', 'tax_class_id' => 3)),
            array(array('id' => 3, 'code' => 'Retailer', 'tax_class_id' => 3))
        );
    }

    public function testCreateGroup()
    {
        $group = (new Data\CustomerGroupBuilder())->setId(null)->setCode('Test Group')->setTaxClassId(4)->create();
        $groupId = $this->_groupService->saveGroup($group);
        $this->assertNotNull($groupId);

        $newGroup = $this->_groupService->getGroup($groupId);
        $this->assertEquals($groupId, $newGroup->getId());
        $this->assertEquals($group->getCode(), $newGroup->getCode());
        $this->assertEquals($group->getTaxClassId(), $newGroup->getTaxClassId());
    }

    public function testUpdateGroup()
    {
        $group = (new Data\CustomerGroupBuilder())->setId(null)->setCode('New Group')->setTaxClassId(4)->create();
        $groupId = $this->_groupService->saveGroup($group);
        $this->assertNotNull($groupId);

        $newGroup = $this->_groupService->getGroup($groupId);
        $this->assertEquals($groupId, $newGroup->getId());
        $this->assertEquals($group->getCode(), $newGroup->getCode());
        $this->assertEquals($group->getTaxClassId(), $newGroup->getTaxClassId());

        $updates = (new Data\CustomerGroupBuilder())->setId(
            $groupId
        )->setCode(
            'Updated Group'
        )->setTaxClassId(
            2
        )->create();
        $newId = $this->_groupService->saveGroup($updates);
        $this->assertEquals($newId, $groupId);
        $updatedGroup = $this->_groupService->getGroup($groupId);
        $this->assertEquals($updates->getCode(), $updatedGroup->getCode());
        $this->assertEquals($updates->getTaxClassId(), $updatedGroup->getTaxClassId());
    }


    /**
     * @param $testGroup
     * @param $storeId
     *
     * @dataProvider getDefaultGroupDataProvider
     */
    public function testGetDefaultGroupWithStoreId($testGroup, $storeId)
    {
        $this->assertDefaultGroupMatches($testGroup, $storeId);
    }


    /**
     * @return array
     *
     */
    public function getDefaultGroupDataProvider()
    {
        /** @var \Magento\Core\Model\StoreManagerInterface  $storeManager */
        $storeManager = Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface');
        $defaultStoreId = $storeManager->getStore()->getId();
        return [
            'no store id' => [['id' => 1, 'code' => 'General', 'tax_class_id' => 3], null],
            'default store id' => [['id' => 1, 'code' => 'General', 'tax_class_id' => 3], $defaultStoreId],
        ];
    }

    /**
     * @magentoDataFixture Magento/Core/_files/second_third_store.php
     */
    public function testGetDefaultGroupWithNonDefaultStoreId()
    {        /** @var \Magento\Core\Model\StoreManagerInterface  $storeManager */
        $storeManager = Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface');
        $nonDefaultStore = $storeManager->getStores()[1];
        $nonDefaultStoreId = $nonDefaultStore->getId();
        $nonDefaultStore->setConfig(Group::XML_PATH_DEFAULT_ID, 2);
        $testGroup = ['id' => 2, 'code' => 'Wholesale', 'tax_class_id' => 3];
        $this->assertDefaultGroupMatches($testGroup, $nonDefaultStoreId);
    }

    /**
     * @expectedException \Magento\Core\Model\Store\Exception
     */
    public function testGetDefaultGroupWithInvalidStoreId()
    {
        $storeId = 1234567;
        $this->_groupService->getDefaultGroup($storeId);
    }

    /**
     * @param Data\Filter[] $filters
     * @param Data\Filter[] $orGroup
     * @param array $expectedResult array of expected results indexed by ID
     *
     * @dataProvider searchGroupsDataProvider
     */
    public function testSearchGroups($filters, $orGroup, $expectedResult)
    {
        $searchBuilder = new Data\SearchCriteriaBuilder();
        foreach ($filters as $filter) {
            $searchBuilder->addFilter($filter);
        }
        if (!is_null($orGroup)) {
            $searchBuilder->addOrGroup($orGroup);
        }

        $searchResults = $this->_groupService->searchGroups($searchBuilder->create());

        /** @var $item Data\CustomerGroup*/
        foreach ($searchResults->getItems() as $item) {
            $this->assertEquals($expectedResult[$item->getId()]['code'], $item->getCode());
            $this->assertEquals($expectedResult[$item->getId()]['tax_class_id'], $item->getTaxClassId());
            unset($expectedResult[$item->getId()]);
        }
    }

    public function searchGroupsDataProvider()
    {
        return array(
            'eq' => array(
                array((new Data\FilterBuilder())->setField('code')->setValue('General')->create()),
                null,
                array(1 => array('code' => 'General', 'tax_class_id' => 3))
            ),
            'and' => array(
                array(
                    (new Data\FilterBuilder())->setField('code')->setValue('General')->create(),
                    (new Data\FilterBuilder())->setField('tax_class_id')->setValue('3')->create(),
                    (new Data\FilterBuilder())->setField('id')->setValue('1')->create()
                ),
                array(),
                array(1 => array('code' => 'General', 'tax_class_id' => 3))
            ),
            'or' => array(
                array(),
                array(
                    (new Data\FilterBuilder())->setField('code')->setValue('General')->create(),
                    (new Data\FilterBuilder())->setField('code')->setValue('Wholesale')->create()
                ),
                array(
                    1 => array('code' => 'General', 'tax_class_id' => 3),
                    2 => array('code' => 'Wholesale', 'tax_class_id' => 3)
                )
            ),
            'like' => array(
                array(
                    (new Data\FilterBuilder())->setField('code')->setValue('er')->setConditionType('like')->create()
                ),
                array(),
                array(
                    1 => array('code' => 'General', 'tax_class_id' => 3),
                    3 => array('code' => 'Retailer', 'tax_class_id' => 3)
                )
            )
        );
    }

    /**
     * @param $testGroup
     * @param $storeId
     */
    private function assertDefaultGroupMatches($testGroup, $storeId)
    {
        $group = $this->_groupService->getDefaultGroup($storeId);
        $this->assertEquals($testGroup['id'], $group->getId());
        $this->assertEquals($testGroup['code'], $group->getCode());
        $this->assertEquals($testGroup['tax_class_id'], $group->getTaxClassId());
    }
}
