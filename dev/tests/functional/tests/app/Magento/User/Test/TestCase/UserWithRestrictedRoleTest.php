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
 *
 */
class UserWithRestrictedRoleTest extends Functional
{
    /**
     * Login into backend area before test
     */
    protected function setUp()
    {
        Factory::getApp()->magentoBackendLoginUser();
    }

    /**
     * Logout admin user
     */
    protected function tearDown()
    {
        Factory::getApp()->magentoBackendLogoutUser();
    }

    /**
     * Test verify "Using ACL Role with full GWS Scope"
     *
     * @ZephyrId MAGETWO-12375
     */
    public function testAclRoleWithFullGwsScope()
    {
        //Set Use Secret key to URLs "No"
        $configFactory = Factory::getFixtureFactory()->getMagentoCoreConfig();
        $configFactory->switchData('disable_secret_key');
        $configFactory->persist();

        //Create new Admin User
        $userFixture = Factory::getFixtureFactory()->getMagentoUserAdminUser();
        $userFixture->switchData('admin_default');
        $userFixture->persist();

        //Create new Acl Role - Role Resources: Sales
        $roleFixture = Factory::getFixtureFactory()->getMagentoUserRole();
        $roleFixture->switchData('custom_permissions_all_scopes');
        $resourceFixture = Factory::getFixtureFactory()->getMagentoUserResource();
        $roleFixture->setResource($resourceFixture->get('Magento_Sales::sales_order'));
        $data = $roleFixture->persist();

        //Pages & Blocks
        $userPage = Factory::getPageFactory()->getAdminUser();
        $editUser = Factory::getPageFactory()->getAdminUserEdit();
        $editForm = $editUser->getUserForm();
        $salesPage = Factory::getPageFactory()->getSalesOrder();
        $catalogProductPage = Factory::getPageFactory()->getCatalogProductIndex();
        $loginPage = Factory::getPageFactory()->getAdminAuthLogin();
        $dashboard = Factory::getPageFactory()->getAdminDashboard();

        //Steps
        $userPage->open();
        $userPage->getUserGrid()->searchAndOpen(['email' => $userFixture->getEmail()]);
        $editForm->openTab('user-role');
        $rolesGrid = $editUser->getRolesGrid();
        $rolesGrid->searchAndOpen(['rolename' => $data['rolename']]);
        $editUser->getPageActions()->save();

        //Verification
        $this->assertContains(
            'You saved the user.',
            $userPage->getMessagesBlock()->getSuccessMessages()
        );
        $dashboard->getAdminPanelHeader()->logOut();

        //Login with newly created admin user
        $loginPage->open();
        $loginPage->getLoginBlock()->fill($userFixture);
        $loginPage->getLoginBlock()->submit();
        $loginPage->waitForHeaderBlock();
        $salesPage->open();

        //Verify that only Sales resource is available.
        $this->assertTrue(
            count($dashboard->getMenuBlock()->getTopMenuItems()) == 1,
            "You have access not only for Sales resource"
        );

        //Verify that if try go to restricted resource via url "Access Denied" page is opened
        $catalogProductPage->open();
        $this->assertContains(
            'Access denied',
            $catalogProductPage->getAccessDeniedBlock()->getTextFromAccessDeniedBlock()
        );
    }

    /**
     * Test "Using ACL Role with restricted GWS Scope"
     *
     * @ZephyrId MAGETWO-12385
     */
    public function testAclRoleWithRestrictedGwsScope()
    {
        //Create new Store
        $storeGroupFixture = Factory::getFixtureFactory()->getMagentoStoreStoreGroup();
        $storeGroupData = $storeGroupFixture->persist();

        $storeFixture = Factory::getFixtureFactory()->getMagentoStoreStore(
            array('store_group' => $storeGroupData['id'])
        );
        $storeFixture->switchData('custom_store');
        $storeData = $storeFixture->persist();

        //Create new Admin User
        $userFixture = Factory::getFixtureFactory()->getMagentoUserAdminUser();
        $userFixture->switchData('admin_default');
        $userFixture->persist();

        //Create new Acl Role - Role Resources: Sales
        $roleFixture = Factory::getFixtureFactory()->getMagentoUserRole();
        $roleFixture->switchData('custom_permissions_store_scope');
        $roleFixture->setScopeItems(array($storeGroupData['id']));

        $resourceFixture = Factory::getFixtureFactory()->getMagentoUserResource();
        $roleFixture->setResource($resourceFixture->get('Magento_Sales::sales_order'));
        $data = $roleFixture->persist();

        //Pages & Blocks
        $userPage = Factory::getPageFactory()->getAdminUser();
        $editUser = Factory::getPageFactory()->getAdminUserEdit();
        $editForm = $editUser->getUserForm();
        $salesPage = Factory::getPageFactory()->getSalesOrder();
        $salesGrid = $salesPage->getOrderGridBlock();
        $catalogProductPage = Factory::getPageFactory()->getCatalogProductIndex();
        $loginPage = Factory::getPageFactory()->getAdminAuthLogin();
        $dashboard = Factory::getPageFactory()->getAdminDashboard();

        //Steps
        $userPage->open();
        $userPage->getUserGrid()->searchAndOpen(['email' => $userFixture->getEmail()]);
        $editForm->openTab('user-role');
        $rolesGrid = $editUser->getRolesGrid();
        $rolesGrid->searchAndOpen(['rolename' => $data['rolename']]);
        $editUser->getPageActions()->save();

        //Verification
        $this->assertContains(
            'You saved the user.',
            $userPage->getMessagesBlock()->getSuccessMessages()
        );
        $dashboard->getAdminPanelHeader()->logOut();

        //Login with newly created admin user
        $loginPage->open();
        $loginPage->getLoginBlock()->fill($userFixture);
        $loginPage->getLoginBlock()->submit();
        $loginPage->waitForHeaderBlock();
        $salesPage->open();

        //Verify that only Sales resource is available.
        $this->assertTrue(
            count($dashboard->getMenuBlock()->getTopMenuItems()) == 1,
            "You have access not only for Sales resource"
        );

        //Verify that at "Purchase Point" dropdown only store from preconditions is available
        $this->assertContains(
            $storeData['name'],
            $salesGrid->getPurchasePointFilterText()
        );
        $this->assertTrue(
            $salesGrid->assertNumberOfPurchasePointFilterOptionsGroup(2),
            "You have more than one store in the Purchase Point Filter"
        );

        //Verify that if try go to restricted resource via url "Access Denied" page is opened
        $catalogProductPage->open();
        $this->assertContains(
            'Access denied',
            $catalogProductPage->getAccessDeniedBlock()->getTextFromAccessDeniedBlock()
        );
    }
}
