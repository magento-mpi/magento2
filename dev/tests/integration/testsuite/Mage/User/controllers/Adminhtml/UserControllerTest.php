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

/**
 * @group module:Mage_User
 */
class Mage_User_Adminhtml_UserControllerTest extends Mage_Adminhtml_Utility_Controller
{
    /**
     * @covers Mage_User_Adminhtml_UserController::indexAction
     */
    public function testIndexAction()
    {
        $this->markTestIncomplete('MAGETWO-1587');
        $this->dispatch('admin/user/index');
        $this->assertStringMatchesFormat('%a<div class="content-header">%aUsers%a', $this->getResponse()->getBody());
    }

    /**
     * @covers Mage_User_Adminhtml_UserController::rolesGridAction
     */
    public function testRoleGridAction()
    {
        $this->markTestIncomplete('MAGETWO-1587');
        $this->getRequest()
            ->setParam('ajax', true)
            ->setParam('isAjax', true);
        $this->dispatch('admin/user/roleGrid');
        $expected = '%a<table %a id="permissionsUserGrid_table">%a';
        $this->assertStringMatchesFormat($expected, $this->getResponse()->getBody());
    }

    /**
     * @covers Mage_User_Adminhtml_UserController::rolesGridAction
     */
    public function testRolesGridAction()
    {
        $this->markTestIncomplete('MAGETWO-1587');
        $this->getRequest()
            ->setParam('ajax', true)
            ->setParam('isAjax', true)
            ->setParam('user_id', 1);
        $this->dispatch('admin/user/rolesGrid');
        $expected = '%a<table %a id="permissionsUserRolesGrid_table">%a';
        $this->assertStringMatchesFormat($expected, $this->getResponse()->getBody());
    }

    /*
     * @covers Mage_User_Adminhtml_UserController::editAction
     */
    public function testEditAction()
    {
        $this->markTestIncomplete('MAGETWO-1587');
        $this->getRequest()->setParam('user_id', 1);
        $this->dispatch('admin/user/edit');
        $expected = '%a<h3 class="icon-head head-user">Edit User%a';
        $this->assertStringMatchesFormat($expected, $this->getResponse()->getBody());
    }
}
