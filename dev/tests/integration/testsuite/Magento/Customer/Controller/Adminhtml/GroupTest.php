<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Adminhtml;

use Magento\Message\MessageInterface;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoAppArea adminhtml
 * @magentoDataFixture customerGroupDataFixture
 */
class GroupTest extends \Magento\Backend\Utility\Controller
{
    const TAX_CLASS_ID = 3;
    const CUSTOMER_GROUP_CODE = 'New Customer Group';

    protected static $_customerGroupId;

    public static function customerGroupDataFixture()
    {
        /** @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService */
        $groupService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');

        $group = new \Magento\Customer\Service\V1\Dto\CustomerGroup([
          'id' => null,
          'code' => self::CUSTOMER_GROUP_CODE,
          'tax_class_id' => self::TAX_CLASS_ID
        ]);
        self::$_customerGroupId = $groupService->saveGroup($group);;
    }

    public function testNewAction()
    {
        $this->dispatch('backend/customer/group/new');
        $responseBody = $this->getResponse()->getBody();
        $this->assertRegExp('/<h1 class\="title">\s*New Customer Group\s*<\/h1>/', $responseBody);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testDeleteActionExistingGroup()
    {
        $this->getRequest()->setParam('id', self::$_customerGroupId);
        $this->dispatch('backend/customer/group/delete');

        /**
         * Check that success message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(['The customer group has been deleted.']), MessageInterface::TYPE_SUCCESS
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
            $this->equalTo(['The customer group no longer exists.']), MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveActionExistingGroup()
    {
        $this->getRequest()->setParam('tax_class', self::TAX_CLASS_ID);
        $this->getRequest()->setParam('id', self::$_customerGroupId);
        $this->getRequest()->setParam('code', self::CUSTOMER_GROUP_CODE);

        $this->dispatch('backend/customer/group/save');

        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->logicalNot($this->isEmpty()), MessageInterface::TYPE_SUCCESS);

        $this->assertSessionMessages(
            $this->equalTo(['The customer group has been saved.']), MessageInterface::TYPE_SUCCESS
        );

        /** @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService */
        $groupService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');
        $customerGroupData = $groupService->getGroup(self::$_customerGroupId)->__toArray();
        ksort($customerGroupData);

        $this->assertEquals(
            [
                'code' => self::CUSTOMER_GROUP_CODE,
                'id' => self::$_customerGroupId,
                'tax_class_id' => self::TAX_CLASS_ID
            ],
            $customerGroupData
        );
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveActionExistingGroupWithEmptyGroupCode()
    {
        $this->getRequest()->setParam('tax_class', self::TAX_CLASS_ID);
        $this->getRequest()->setParam('id', self::$_customerGroupId);
        $this->getRequest()->setParam('code', '');

        $this->dispatch('backend/customer/group/save');

        $this->assertSessionMessages(
            $this->equalTo(['The customer group has been saved.']), MessageInterface::TYPE_SUCCESS
        );

        /** @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService */
        $groupService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');

        $this->assertEmpty($groupService->getGroup(self::$_customerGroupId)->getCode());
    }

    public function testSaveActionForwardNewCreateNewGroup()
    {
        $this->dispatch('backend/customer/group/save');
        $responseBody = $this->getResponse()->getBody();
        $this->assertRegExp('/<h1 class\="title">\s*New Customer Group\s*<\/h1>/', $responseBody);
    }

    public function testSaveActionForwardNewEditExistingGroup()
    {
        $this->getRequest()->setParam('id', self::$_customerGroupId);
        $this->dispatch('backend/customer/group/save');

        /** @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService */
        $groupService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');
        $customerGroupCode = $groupService->getGroup(self::$_customerGroupId)->getCode();

        $responseBody = $this->getResponse()->getBody();
        $this->assertRegExp('/<h1 class\="title">\s*' . $customerGroupCode . '\s*<\/h1>/', $responseBody);
    }

    public function testSaveActionNonExistingGroupId()
    {
        $this->getRequest()->setParam('id', 10000);
        $this->getRequest()->setParam('tax_class', self::TAX_CLASS_ID);

        $this->dispatch('backend/customer/group/save');

        $this->assertSessionMessages($this->logicalNot($this->isEmpty()), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->equalTo(['No such entity with groupId = 10000']), MessageInterface::TYPE_ERROR
        );

        /** @var \Magento\Session\SessionManagerInterface $sessionManager */
        $sessionManager = Bootstrap::getObjectManager()->get('Magento\Session\SessionManagerInterface');
        $this->assertEmpty($sessionManager->getCustomerGroupData());
    }
}
