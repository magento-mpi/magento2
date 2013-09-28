<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Controller\Adminhtml\User;

/**
 * Test class for \Magento\User\Controller\Adminhtml\User\Role.
 *
 * @magentoAppArea adminhtml
 */
class RoleTest extends \Magento\Backend\Utility\Controller
{
    public function testEditRoleAction()
    {
        $roleAdmin = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\User\Model\Role');
        $roleAdmin->load(\Magento\TestFramework\Bootstrap::ADMIN_ROLE_NAME, 'role_name');

        $this->getRequest()->setParam('rid', $roleAdmin->getId());

        $this->dispatch('backend/admin/user_role/editrole');

        $this->assertContains('Role Information', $this->getResponse()->getBody());
        $this->assertContains($roleAdmin->getRoleName(), $this->getResponse()->getBody());
    }

    /**
     * @covers \Magento\User\Controller\Adminhtml\User\Role::editrolegridAction
     */
    public function testEditrolegridAction()
    {
        $this->getRequest()
            ->setParam('ajax', true)
            ->setParam('isAjax', true);
        $this->dispatch('backend/admin/user_role/editrolegrid');
        $expected = '%a<table %a id="roleUserGrid_table">%a';
        $this->assertStringMatchesFormat($expected, $this->getResponse()->getBody());
    }

    /**
     * @covers \Magento\User\Controller\Adminhtml\User\Role::roleGridAction
     */
    public function testRoleGridAction()
    {
        $this->getRequest()
            ->setParam('ajax', true)
            ->setParam('isAjax', true)
            ->setParam('user_id', 1);
        $this->dispatch('backend/admin/user_role/roleGrid');
        $expected = '%a<table %a id="roleGrid_table">%a';
        $this->assertStringMatchesFormat($expected, $this->getResponse()->getBody());
    }
}
