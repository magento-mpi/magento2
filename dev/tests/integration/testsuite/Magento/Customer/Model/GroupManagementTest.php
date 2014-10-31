<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Store\Model\ScopeInterface;

/**
 * Test for \Magento\Customer\Model\GroupManagement
 */
class GroupManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Customer\Model\GroupManagement
     */
    protected $groupManagement;

    protected function setUp()
    {
        $this->objectManager = Bootstrap::getObjectManager();
        $this->groupManagement = $this->objectManager->get('Magento\Customer\Model\GroupManagement');
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
     * @magentoDataFixture Magento/Core/_files/second_third_store.php
     */
    public function testGetDefaultGroupWithNonDefaultStoreId()
    {        /** @var \Magento\Framework\StoreManagerInterface  $storeManager */
        $storeManager = Bootstrap::getObjectManager()->get('Magento\Framework\StoreManagerInterface');
        $nonDefaultStore = $storeManager->getStore('secondstore');
        $nonDefaultStoreId = $nonDefaultStore->getId();
        /** @var \Magento\Framework\App\MutableScopeConfig $scopeConfig */
        $scopeConfig = $this->objectManager->get('Magento\Framework\App\MutableScopeConfig');
        $scopeConfig->setValue(
            \Magento\Customer\Api\GroupManagementInterface::XML_PATH_DEFAULT_ID,
            2,
            ScopeInterface::SCOPE_STORE,
            'secondstore'
        );
        $testGroup = ['id' => 2, 'code' => 'Wholesale', 'tax_class_id' => 3];
        $this->assertDefaultGroupMatches($testGroup, $nonDefaultStoreId);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetDefaultGroupWithInvalidStoreId()
    {
        $storeId = 1234567;
        $this->groupManagement->getDefaultGroup($storeId);
    }

    public function testIsReadonlyWithGroupId()
    {
        $testGroup = ['id' => 3, 'code' => 'General', 'tax_class_id' => 3];
        $this->assertEquals(true, $this->groupManagement->isReadonly($testGroup['id']));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testIsReadonlyWithInvalidGroupId()
    {
        $testGroup = ['id' => 4, 'code' => 'General', 'tax_class_id' => 3];
        $this->groupManagement->isReadonly($testGroup['id']);
    }

    /**
     * @return array
     */
    public function getDefaultGroupDataProvider()
    {
        /** @var \Magento\Framework\StoreManagerInterface  $storeManager */
        $storeManager = Bootstrap::getObjectManager()->get('Magento\Framework\StoreManagerInterface');
        $defaultStoreId = $storeManager->getStore()->getId();
        return [
            'no store id' => [['id' => 1, 'code' => 'General', 'tax_class_id' => 3], null],
            'default store id' => [['id' => 1, 'code' => 'General', 'tax_class_id' => 3], $defaultStoreId],
        ];
    }

    /**
     * @param $testGroup
     * @param $storeId
     */
    private function assertDefaultGroupMatches($testGroup, $storeId)
    {
        $group = $this->groupManagement->getDefaultGroup($storeId);
        $this->assertEquals($testGroup['id'], $group->getId());
        $this->assertEquals($testGroup['code'], $group->getCode());
        $this->assertEquals($testGroup['tax_class_id'], $group->getTaxClassId());
    }
}
