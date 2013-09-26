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

/**
 * Test class for Magento_User_Controller_Adminhtml_User_Role.
 *
 * @magentoAppArea adminhtml
 */
class Magento_User_Controller_Adminhtml_User_RoleTest extends Magento_Backend_Utility_Controller
{
    public function testEditRoleAction()
    {
        $roleAdmin = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_User_Model_Role');
        $roleAdmin->load(Magento_TestFramework_Bootstrap::ADMIN_ROLE_NAME, 'role_name');

        $this->getRequest()->setParam('rid', $roleAdmin->getId());

        $this->dispatch('backend/admin/user_role/editrole');

        $this->assertContains('Role Information', $this->getResponse()->getBody());
        $this->assertContains($roleAdmin->getRoleName(), $this->getResponse()->getBody());
    }

    /**
     * @covers Magento_User_Controller_Adminhtml_User_Role::editrolegridAction
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
     * @covers Magento_User_Controller_Adminhtml_User_Role::roleGridAction
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
