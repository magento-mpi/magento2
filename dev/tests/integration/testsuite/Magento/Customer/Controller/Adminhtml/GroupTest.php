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
    const CUSTOMER_GROUP_CODE = 'custom_group';
    const BASE_CONTROLLER_URL = 'http://localhost/index.php/backend/customer/group/';
    const CUSTOMER_GROUP_ID = 2;

    public function testNewAction()
    {
        $this->dispatch('backend/customer/group/new');
        $responseBody = $this->getResponse()->getBody();
        $this->assertRegExp('/<h1 class\="title">\s*New Customer Group\s*<\/h1>/', $responseBody);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testDeleteActionNoGroupId()
    {
        $this->dispatch('backend/customer/group/delete');
        $this->assertRedirect($this->stringStartsWith(self::BASE_CONTROLLER_URL));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testDeleteActionExistingGroup()
    {
        $this->getRequest()->setParam('id', self::CUSTOMER_GROUP_ID);
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

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
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
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testSaveActionExistingGroup()
    {
        $this->getRequest()->setParam('tax_class', self::TAX_CLASS_ID);
        $this->getRequest()->setParam('id', self::CUSTOMER_GROUP_ID);
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
            $groupService->getGroup(self::CUSTOMER_GROUP_ID)
        );
        ksort($customerGroupData);

        $this->assertEquals(
            [
                'code' => self::CUSTOMER_GROUP_CODE,
                'id' => self::CUSTOMER_GROUP_ID,
                'tax_class_id' => self::TAX_CLASS_ID
            ],
            $customerGroupData
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testSaveActionExistingGroupWithEmptyGroupCode()
    {
        /** @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService */
        $groupService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');

        $originalCode = $groupService->getGroup(self::CUSTOMER_GROUP_ID)->getCode();

        $this->getRequest()->setParam('tax_class', self::TAX_CLASS_ID);
        $this->getRequest()->setParam('id', self::CUSTOMER_GROUP_ID);
        $this->getRequest()->setParam('code', '');

        $this->dispatch('backend/customer/group/save');

        $this->assertSessionMessages(
            $this->equalTo(['Invalid value of "" provided for the code field.']),
            MessageInterface::TYPE_ERROR
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_SUCCESS);
        $this->assertEquals($originalCode, $groupService->getGroup(self::CUSTOMER_GROUP_ID)->getCode());
    }

    public function testSaveActionForwardNewCreateNewGroup()
    {
        $this->dispatch('backend/customer/group/save');
        $responseBody = $this->getResponse()->getBody();
        $this->assertRegExp('/<h1 class\="title">\s*New Customer Group\s*<\/h1>/', $responseBody);
    }

    public function testSaveActionForwardNewEditExistingGroup()
    {
        $this->getRequest()->setParam('id', self::CUSTOMER_GROUP_ID);
        $this->dispatch('backend/customer/group/save');

        /** @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService */
        $groupService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');
        $customerGroupCode = $groupService->getGroup(self::CUSTOMER_GROUP_ID)->getCode();

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
        $this->assertRedirect($this->stringStartsWith(self::BASE_CONTROLLER_URL . 'edit/'));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testSaveActionNewGroupWithExistingGroupCode()
    {
        /** @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService */
        $groupService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');

        $originalCode = $groupService->getGroup(self::CUSTOMER_GROUP_ID)->getCode();

        $this->getRequest()->setParam('tax_class', self::TAX_CLASS_ID);
        $this->getRequest()->setParam('code', self::CUSTOMER_GROUP_CODE);

        $this->dispatch('backend/customer/group/save');

        $this->assertSessionMessages($this->equalTo(['Customer Group already exists.']), MessageInterface::TYPE_ERROR);
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_SUCCESS);

        $this->assertEquals($originalCode, $groupService->getGroup(self::CUSTOMER_GROUP_ID)->getCode());
        $this->assertRedirect($this->stringStartsWith(self::BASE_CONTROLLER_URL . 'edit/'));
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer_group.php
     */
    public function testSaveActionNewGroupWithoutGroupCode()
    {
        /** @var \Magento\Customer\Service\V1\CustomerGroupServiceInterface $groupService */
        $groupService = Bootstrap::getObjectManager()
            ->get('Magento\Customer\Service\V1\CustomerGroupServiceInterface');

        $originalCode = $groupService->getGroup(self::CUSTOMER_GROUP_ID)->getCode();

        $this->getRequest()->setParam('tax_class', self::TAX_CLASS_ID);

        $this->dispatch('backend/customer/group/save');

        $this->assertSessionMessages(
            $this->equalTo(['Invalid value of "" provided for the code field.']),
            MessageInterface::TYPE_ERROR
        );
        $this->assertSessionMessages($this->isEmpty(), MessageInterface::TYPE_SUCCESS);

        $this->assertEquals($originalCode, $groupService->getGroup(self::CUSTOMER_GROUP_ID)->getCode());
        $this->assertRedirect($this->stringStartsWith(self::BASE_CONTROLLER_URL . 'edit/'));
    }
}
