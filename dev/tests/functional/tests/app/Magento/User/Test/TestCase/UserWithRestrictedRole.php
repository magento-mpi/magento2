<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\TestCase;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class UserWithRestrictedRole
 * Test verify "Using ACL Role with full GWS Scope"
 * @package Magento\User\Test\TestCase
 */
class UserWithRestrictedRole extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    public function testAclRoleWithFullGwsScope()
    {
        $this->markTestSkipped('MAGETWO-17744');
        //Create new Admin User
        $userFixture = Factory::getFixtureFactory()->getMagentoUserAdminUser();
        $userFixture->switchData('admin_default');
        $user = $userFixture->persist();
        //Create new Acl Role - Role Resources: Sales
        $roleFixture = Factory::getFixtureFactory()->getMagentoUserRole();
        $roleFixture->switchData('role_sales');
        $data = $roleFixture->persist();
        //Pages & Blocks
        $userPage = Factory::getPageFactory()->getAdminUser();
        $editUser = Factory::getPageFactory()->getAdminUserEditUserId();
        $editForm = $editUser->getEditForm();
        $salesPage = Factory::getPageFactory()->getSalesOrder();
        $catalogProductPage = Factory::getPageFactory()->getCatalogProductIndex();
        $loginPage = Factory::getPageFactory()->getAdminAuthLogin();
        //Steps
        $userPage->open();
        $userPage->getUserGrid()->searchAndOpen(array('email' => $userFixture->getEmail()));
        $editForm->openRoleTab();
        $editUser->getRoleGrid()->setRole($data['rolename'], $data['id']);
        $editForm->save();
        //Verification
        $this->assertContains('You saved the user.', $userPage->getMessagesBlock()->getSuccessMessages());
        $userPage->getAdminPanelHeader()->logOut();
        //Login with newly created admin user
        $userFixture->setPassword($userFixture->getPassword());
        $loginPage->getLoginBlockForm()->fill($userFixture);
        $loginPage->getLoginBlockForm()->submit();
        $salesPage->open();
        //Verify that only Sales resource is available.
        $this->assertEquals(1, count($salesPage->getNavigationMenuItems()),
            "You have access not only for Sales resource");
        //Verify that if try go to restricted resource via url "Access Denied" page is opened
        $catalogProductPage->open();
        $this->assertContains('Access denied',
            $catalogProductPage->getAccessDeniedBlock()->getTextFromAccessDeniedBlock());
    }
}

