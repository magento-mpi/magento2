<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Controller\Adminhtml;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Message\MessageInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Customer\Service\V1\Data\CustomerGroupBuilder;
use Magento\Customer\Service\V1\CustomerGroupServiceInterface;

/**
 * @magentoAppArea adminhtml
 */
class GroupTest extends \Magento\Backend\Utility\Controller
{
    const TAX_CLASS_ID = 3;
    const CUSTOMER_GROUP_CODE = 'New Customer Group';
    const BASE_CONTROLLER_URL = 'http://localhost/index.php/backend/customer/group/';

    protected static $_customerGroupId;

    public static function setUpBeforeClass()
    {
        /** @var CustomerGroupServiceInterface $groupService */
        $groupService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');

        /** @var CustomerGroupBuilder $groupBuilder */
        $groupBuilder = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\Data\CustomerGroupBuilder');
        $group = $groupBuilder->populateWithArray([
                'id' => null,
                'code' => self::CUSTOMER_GROUP_CODE,
                'tax_class_id' => self::TAX_CLASS_ID
            ])
            ->create();
        self::$_customerGroupId = $groupService->saveGroup($group);;
    }

    public static function tearDownAfterClass()
    {
        /** @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService */
        $groupService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');
        $groupService->deleteGroup(self::$_customerGroupId);
    }

    public function testNewAction()
    {
        $this->dispatch('backend/customer/group/new');
        $responseBody = $this->getResponse()->getBody();
        $this->assertRegExp('/<h1 class\="title">\s*New Customer Group\s*<\/h1>/', $responseBody);
    }

    public function testDeleteActionNoGroupId()
    {
        $this->dispatch('backend/customer/group/delete');
        $this->assertRedirect($this->stringStartsWith(self::BASE_CONTROLLER_URL));
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
            $this->equalTo(['The customer group has been deleted.']),
            MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringStartsWith(self::BASE_CONTROLLER_URL . 'index'));
    }

    public function testDeleteActionNonExistingGroupId()
    {
        $this->getRequest()->setParam('id', 10000);
        $this->dispatch('backend/customer/group/delete');

        /**
         * Check that error message is set
         */
        $this->assertSessionMessages(
            $this->equalTo(['The customer group no longer exists.']),
            MessageInterface::TYPE_ERROR
        );
        $this->assertRedirect($this->stringStartsWith(self::BASE_CONTROLLER_URL));
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
            $this->equalTo(['The customer group has been saved.']),
            MessageInterface::TYPE_SUCCESS
        );

        /** @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService */
        $groupService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');
        $customerGroupData = \Magento\Service\DataObjectConverter::toFlatArray(
            $groupService->getGroup(self::$_customerGroupId)
        );
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
        /** @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService */
        $groupService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');

        $originalCode = $groupService->getGroup(self::$_customerGroupId)->getCode();

        $this->getRequest()->setParam('tax_class', self::TAX_CLASS_ID);
        $this->getRequest()->setParam('id', self::$_customerGroupId);
        $this->getRequest()->setParam('code', '');

        $this->dispatch('backend/customer/group/save');

        $this->assertSessionMessages($this->equalTo(["One or more input exceptions have occurred.\n"
          . "{\n\tcode: INVALID_FIELD_VALUE\n\tcode: \n\tparams: []\n }\n"]), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_SUCCESS);

        $this->assertEquals($originalCode, $groupService->getGroup(self::$_customerGroupId)->getCode());
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

        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_SUCCESS);
        $this->assertSessionMessages($this->logicalNot($this->isEmpty()), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages(
            $this->equalTo(['No such entity with groupId = 10000']),
            MessageInterface::TYPE_ERROR
        );

        /** @var \MagentoRegistry $coreRegistry */
        $coreRegistry = Bootstrap::getObjectManager()->get('Magento\Registry');
        $this->assertNull($coreRegistry->registry(RegistryConstants::CURRENT_GROUP_ID));

        $this->assertRedirect($this->stringStartsWith(self::BASE_CONTROLLER_URL . 'edit/id/10000'));
    }
}
