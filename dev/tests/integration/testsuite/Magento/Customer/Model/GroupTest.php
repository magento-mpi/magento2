<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model;

class GroupTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\Group
     */
    protected $groupModel;

    /**
     * @var \Magento\Customer\Api\Data\GroupDataBuilder
     */
    protected $groupBuilder;

    protected function setUp()
    {
        $this->groupModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Model\Group'
        );
        $this->groupBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Api\Data\GroupDataBuilder'
        );
    }

    public function testCRUD()
    {
        $this->groupModel->setCode('test');
        $crud = new \Magento\TestFramework\Entity($this->groupModel, array('customer_group_code' => uniqid()));
        $crud->testCrud();
    }

    public function testUpdateDataSetDataOnEmptyModel()
    {
        /** @var \Magento\Customer\Model\Data\Group $groupData */
        $groupData = $this->groupBuilder
            ->setId(1)
            ->setCode('foo')
            ->setTaxClassId(1)
            ->setTaxClassName('bar')
            ->create();
        $groupData = $this->groupModel->updateData($groupData)->getDataModel();

        $this->assertEquals(1, $groupData->getId());
        $this->assertEquals('foo', $groupData->getCode());
        $this->assertEquals(1, $groupData->getTaxClassId());
        $this->assertEquals('bar', $groupData->getTaxClassName());
    }

    public function testUpdateDataOverrideExistingData()
    {
        /** @var \Magento\Customer\Model\Data\Group $groupData */
        $groupData = $this->groupBuilder
            ->setId(2)
            ->setCode('foo')
            ->setTaxClassId(2)
            ->setTaxClassName('bar')
            ->create();
        $this->groupModel->updateData($groupData);

        /** @var \Magento\Customer\Model\Data\Group $groupData */
        $updatedGroupData = $this->groupBuilder
            ->setId(3)
            ->setCode('baz')
            ->setTaxClassId(4)
            ->setTaxClassName('qux')
            ->create();
        $updatedGroupData = $this->groupModel->updateData($updatedGroupData)->getDataModel();

        $this->assertEquals(3, $updatedGroupData->getId());
        $this->assertEquals('baz', $updatedGroupData->getCode());
        $this->assertEquals(4, $updatedGroupData->getTaxClassId());
        $this->assertEquals('qux', $updatedGroupData->getTaxClassName());
    }
}
