<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model;

/**
 * Test for \Magento\Customer\Model\GroupRegistry
 */
class GroupRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The group code from the fixture data.
     */
    const GROUP_CODE = 'custom_group';

    /**
     * @var \Magento\Customer\Model\GroupRegistry
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\GroupRegistry');
    }

    /**
     * Find the group with a given code.
     *
     * @param string $code 
     * @return int
     */
    protected function _findGroupIdWithCode($code)
    {
        $groupService = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Service\V1\CustomerGroupService');
        foreach ($groupService->getGroups() as $group) {
            if ($group->getCode() === $code) {
                return $group->getId();
            }
        }

        return -1;
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testRetrieve()
    {
        $groupId = $this->_findGroupIdWithCode(self::GROUP_CODE);
        $group = $this->_model->retrieve($groupId);
        $this->assertInstanceOf('\Magento\Customer\Model\Group', $group);
        $this->assertEquals($groupId, $group->getId());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testRetrieveCached()
    {
        $groupId = $this->_findGroupIdWithCode(self::GROUP_CODE);
        $groupBeforeDeletion = $this->_model->retrieve($groupId);
        $group2 = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Customer\Model\Group');
        $group2->load($groupId)
            ->delete();
        $groupAfterDeletion = $this->_model->retrieve($groupId);
        $this->assertEquals($groupBeforeDeletion, $groupAfterDeletion);
        $this->assertInstanceOf('\Magento\Customer\Model\Group', $groupAfterDeletion);
        $this->assertEquals($groupId, $groupAfterDeletion->getId());
    }

    /**
     * @expectedException \Magento\Exception\NoSuchEntityException
     */
    public function testRetrieveException()
    {
        $groupId = $this->_findGroupIdWithCode(self::GROUP_CODE);
        $this->_model->retrieve($groupId);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     * @expectedException \Magento\Exception\NoSuchEntityException
     */
    public function testRemove()
    {
        $groupId = $this->_findGroupIdWithCode(self::GROUP_CODE);
        $group = $this->_model->retrieve($groupId);
        $this->assertInstanceOf('\Magento\Customer\Model\Group', $group);
        $group->delete();
        $this->_model->remove($groupId);
        $this->_model->retrieve($groupId);
    }
}
