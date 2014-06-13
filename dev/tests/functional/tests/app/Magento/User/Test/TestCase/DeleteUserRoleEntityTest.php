<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\TestCase;

use Magento\Backend\Test\Page\AdminAuthLogin;
use Magento\Backend\Test\Page\Dashboard;
use Magento\User\Test\Fixture\AdminUserInjectable;
use Magento\User\Test\Fixture\AdminUserRole;
use Magento\User\Test\Page\Adminhtml\UserRoleIndex;
use Magento\User\Test\Page\Adminhtml\UserRoleEditRole;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for DeleteUserRoleEntity
 *
 * Test Flow:
 * Preconditions:
 *  1.Create new admin user and assign it to new role.
 * Steps:
 *  1. Log in as admin user from data set.
 *  2. Go to System>Permissions>User Roles
 *  3. Open role created in precondition
 *  4. Click "Delete Role" button
 *  5. Perform all assertions
 *
 * @group ACL_(MX)
 * @ZephyrId MAGETWO-23926
 */
class DeleteUserRoleEntityTest extends Injectable
{
    /**
     * @var UserRoleIndex
     */
    protected $userRoleIndex;

    /**
     * @var UserRoleEditRole
     */
    protected $userRoleEditRole;

    /**
     * @var AdminAuthLogin
     */
    protected $adminAuthLogin;

    /**
     * @var Dashboard
     */
    protected $dashboard;

    /**
     * Preconditions for test
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        /** @var \Magento\User\Test\Fixture\AdminUserRole  $role */
        $role = $fixtureFactory->createByCode('adminUserRole', ['dataSet' => 'default']);
        $role->persist();
        $roleId = $role->getRoleId();
        $adminUser = $fixtureFactory->createByCode(
            'adminUserInjectable',
            [
                'dataSet' => 'custom_admin',
                'data' => ['role_id' => $roleId]
            ]
        );
        $adminUser->persist();

        return [
            'role' => $role,
            'adminUser' => $adminUser
        ];
    }

    /**
     * @param UserRoleIndex $userRoleIndex
     * @param UserRoleEditRole $userRoleEditRole
     * @param AdminAuthLogin $adminAuthLogin
     * @param Dashboard $dashboard
     */
    public function __inject(
        UserRoleIndex $userRoleIndex,
        UserRoleEditRole $userRoleEditRole,
        AdminAuthLogin $adminAuthLogin,
        Dashboard $dashboard
    ) {
        $this->userRoleIndex = $userRoleIndex;
        $this->userRoleEditRole = $userRoleEditRole;
        $this->adminAuthLogin = $adminAuthLogin;
        $this->dashboard = $dashboard;
    }

    /**
     * Runs Delete User Role Entity test.
     *
     * @param AdminUserRole $role
     * @param AdminUserInjectable $adminUser
     * @param string $isDefaultUser
     * @return void
     */
    public function testDeleteAdminUserRole(
        AdminUserRole $role,
        AdminUserInjectable $adminUser,
        $isDefaultUser
    ) {
        $filter = [
            'rolename' => $role->getRoleName()
        ];
        //Steps
        if ($isDefaultUser == 0) {
            $this->adminAuthLogin->open();
            $this->adminAuthLogin->getLoginBlock()->fill($adminUser);
            $this->adminAuthLogin->getLoginBlock()->submit();
        }
        $this->userRoleIndex->open();
        $this->userRoleIndex->getRoleGrid()->searchAndOpen($filter);
        $this->userRoleEditRole->getPageActions()->delete();
    }

    /**
     * Logout Admin User from account
     *
     * return void
     */
    public function tearDown()
    {
        $this->dashboard->getAdminPanelHeader()->logOut();
    }
}
