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
 * Test class for Mage_User_Adminhtml_User_RoleController.
 *
 * @group module:Mage_User
 */
class Mage_User_Adminhtml_User_RoleControllerTest extends Mage_Adminhtml_Utility_Controller
{
    public function testEditRoleAction()
    {
        $roleAdmin = new Mage_User_Model_Role();
        $roleAdmin->load(Magento_Test_Bootstrap::ADMIN_ROLE_NAME, 'role_name');

        $this->getRequest()->setParam('rid', $roleAdmin->getId());

        $this->dispatch('admin/user_role/editrole');

        $this->assertContains('Role Information', $this->getResponse()->getBody());
        $this->assertContains("Edit Role '" . $roleAdmin->getRoleName() . "'", $this->getResponse()->getBody());
    }
}
