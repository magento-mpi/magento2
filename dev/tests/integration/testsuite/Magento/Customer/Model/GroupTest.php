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
     * @var \Magento\Customer\Model\Data\GroupBuilder
     */
    protected $groupBuilder;

    protected function setUp()
    {
        $this->groupModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Model\Group'
        );
        $this->groupBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Model\Data\GroupBuilder'
        );
    }

    public function testCRUD()
    {
        $this->groupModel->setCode('test');
        $crud = new \Magento\TestFramework\Entity($this->groupModel, array('customer_group_code' => uniqid()));
        $crud->testCrud();
    }

    public function testGetDataModel()
    {
        /** @var \Magento\Customer\Model\Data\Group $groupData */
        $groupData = $this->groupBuilder
            ->setId(1)
            ->setCode('foo')
            ->setTaxClassId(1)
            ->setTaxClassName('bar')
            ->create();
        $updatedGroupData = $this->groupModel->updateData($groupData)->getDataModel();

        $this->assertEquals(1, $updatedGroupData->getId());
        $this->assertEquals('foo', $updatedGroupData->getCode());
        $this->assertEquals(1, $updatedGroupData->getTaxClassId());
        $this->assertEquals('bar', $updatedGroupData->getTaxClassName());
    }
}
