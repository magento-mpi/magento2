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
     * @param FixtureFactory $fixtureFactory
     * @param UserRoleIndex $userRoleIndex
     * @param UserRoleEditRole $userRoleEditRole
     * @param AdminAuthLogin $adminAuthLogin
     * @param AdminUserRole $role
     * @param Dashboard $dashboard
     * @return array
     */
    public function __inject(
        FixtureFactory $fixtureFactory,
        UserRoleIndex $userRoleIndex,
        UserRoleEditRole $userRoleEditRole,
        AdminAuthLogin $adminAuthLogin,
        Dashboard $dashboard
    ) {
        $this->userRoleIndex = $userRoleIndex;
        $this->userRoleEditRole = $userRoleEditRole;
        $this->adminAuthLogin = $adminAuthLogin;
        $this->dashboard = $dashboard;

        $role = $fixtureFactory->createByCode('adminUserRole', ['dataSet' => 'default']);
        $role->persist();
        $role_id = $role->getData('role_id');
        $adminUser = $fixtureFactory->createByCode(
        'adminUserInjectable',
            [
                'dataSet' => 'custom_admin',
                'data' => ['role_id' => $role_id]
            ]
        );
        $adminUser->persist();

        return [
            'role' => $role,
            'adminUserInjectable' => $adminUser
        ];
    }

    /**
     * Runs Delete User Role Entity test.
     *
     * @param AdminUserRole $role
     * @param AdminUserInjectable $adminUserInjectable
     * @param string $isDefaultUser
     * @return void
     */
    public function testDeleteAdminUserRole(
        AdminUserRole $role,
        AdminUserInjectable $adminUserInjectable,
        $isDefaultUser
    ) {
        $filter = [
            'role_name' => $role->getRoleName()
        ];
        //Steps
        if ($isDefaultUser == 0) {
            $this->adminAuthLogin->open();
            $this->adminAuthLogin->getLoginBlock()->fill($adminUserInjectable);
            $this->adminAuthLogin->getLoginBlock()->submit();
            $this->userRoleIndex->open();
        } else {
            $this->userRoleIndex->open();
        }
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
