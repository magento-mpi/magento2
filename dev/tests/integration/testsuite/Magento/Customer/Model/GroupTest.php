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
}
