<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Customer\Controller\Adminhtml;

/**
 * @magentoAppArea adminhtml
 * @magentoDataFixture customerGroupDataFixture
 */
class GroupTest extends \Magento\Backend\Utility\Controller
{
    protected static $_customerGroupId;

    public static function customerGroupDataFixture()
    {
        /** @var \Magento\Customer\Service\CustomerGroupV1Interface $groupService */
        $groupService = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\CustomerGroupV1Interface');

        $group = new \Magento\Customer\Service\Entity\V1\CustomerGroup([
          'id' => null,
          'code' => 'New Customer Group',
          'tax_class_id' => 3
        ]);
        self::$_customerGroupId = $groupService->saveGroup($group);;
    }

    public function testNewAction()
    {
        $this->dispatch('backend/customer/group/new');
        $responseBody = $this->getResponse()->getBody();
        $this->assertRegExp('/<h1 class\="title">\s*New Customer Group\s*<\/h1>/', $responseBody);
    }

    public function testDeleteActionExistingGroup()
    {
        $this->getRequest()->setParam('id', self::$_customerGroupId);
        $this->dispatch('backend/customer/group/delete');

        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('The customer group has been deleted.')),
            \Magento\Message\MessageInterface::TYPE_SUCCESS
        );
    }

    public function testDeleteActionNonExistingGroupId()
    {
        $this->getRequest()->setParam('id', 10000);
        $this->dispatch('backend/customer/group/delete');

        /**
         * Check that error message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(array('The customer group no longer exists.')), \Magento\Message\MessageInterface::TYPE_ERROR
        );
    }
}
