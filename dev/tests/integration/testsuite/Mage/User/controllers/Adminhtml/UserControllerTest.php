<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_User_Adminhtml_UserControllerTest extends Mage_Backend_Utility_Controller
{
    /**
     * @covers Mage_User_Adminhtml_UserController::indexAction
     */
    public function testIndexAction()
    {
        $this->dispatch('backend/admin/user/index');
        $response = $this->getResponse()->getBody();
        $this->assertContains('Users', $response);
        $this->assertSelectCount('#permissionsUserGrid_table', 1, $response);
    }

    /**
     * @covers Mage_User_Adminhtml_UserController::rolesGridAction
     */
    public function testRoleGridAction()
    {
        $this->getRequest()
            ->setParam('ajax', true)
            ->setParam('isAjax', true);
        $this->dispatch('backend/admin/user/roleGrid');
        $expected = '%a<table %a id="permissionsUserGrid_table">%a';
        $this->assertStringMatchesFormat($expected, $this->getResponse()->getBody());
    }

    /**
     * @covers Mage_User_Adminhtml_UserController::rolesGridAction
     */
    public function testRolesGridAction()
    {
        $this->getRequest()
            ->setParam('ajax', true)
            ->setParam('isAjax', true)
            ->setParam('user_id', 1);
        $this->dispatch('backend/admin/user/rolesGrid');
        $expected = '%a<table %a id="permissionsUserRolesGrid_table">%a';
        $this->assertStringMatchesFormat($expected, $this->getResponse()->getBody());
    }

    /**
     * @covers Mage_User_Adminhtml_UserController::editAction
     */
    public function testEditAction()
    {
        $this->getRequest()->setParam('user_id', 1);
        $this->dispatch('backend/admin/user/edit');
        $response = $this->getResponse()->getBody();
        //check "User Information" header and fieldset
        $this->assertContains('data-ui-id="adminhtml-user-edit-tabs-title"', $response);
        $this->assertContains('User Information', $response);
        $this->assertSelectCount('#user_base_fieldset', 1, $response);
    }
}
