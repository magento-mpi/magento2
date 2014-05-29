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
use Magento\User\Test\Page\Adminhtml\UserRoleIndex;
use Magento\User\Test\Page\Adminhtml\UserRoleEditRole;
use Magento\User\Test\Fixture\AdminUserInjectable;
use Magento\User\Test\Fixture\AdminUserRole;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for UpdateAdminUserRoleEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create new admin user and assign it to new role.
 * Steps:
 * 1. Log in as admin user from data set.
 * 2. Go to System>Permissions>User Roles
 * 3. Open role created in precondition
 * 4. Fill in data according to data set
 * 5. Perform all assertions
 *
 * @group ACL_(PS)
 * @ZephyrId MAGETWO-24768
 */
class UpdateAdminUserRoleEntityTest extends Injectable
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
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $role = $fixtureFactory->createByCode('adminUserRole', ['dataSet' => 'default']);
        $role->persist();

        $role_id = $role->getData('role_id');
        $customAdmin = $fixtureFactory->createByCode(
            'adminUserInjectable',
            [
                'dataSet' => 'custom_admin',
                'data' => ['role_id' => $role_id]
            ]
        );
        $customAdmin->persist();

        $userWithOutRole = $fixtureFactory->createByCode('adminUserInjectable',['dataSet' => 'custom_admin']);
        $userWithOutRole->persist();

        return [
            'roleInit' => $role,
            'customAdmin' => $customAdmin,
            'userWithOutRole' => $userWithOutRole
        ];
    }

    /**
     * Preparing pages for test
     *
     * @param UserRoleIndex $userRoleIndex
     * @param UserRoleEditRole $userRoleEditRole
     * @param AdminAuthLogin $adminAuthLogin
     * @param Dashboard $dashboard
     * @return void
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
     * Runs Update Admin User Roles Entity test
     *
     * @param AdminUserRole $roleInit
     * @param AdminUserRole $role
     * @param AdminUserInjectable $userInit
     * @param AdminUserInjectable $userWithOutRole
     * @return void
     */
    public function testUpdateAdminUserRolesEntity(
        AdminUserRole $roleInit,
        AdminUserRole $role,
        AdminUserInjectable $customAdmin,
        AdminUserInjectable $userWithOutRole
    ) {
        $filter = [
            'role_name' => $roleInit->getRoleName()
        ];

        // Steps:
        if ($customAdmin->getUsername() != 'admin') {
            $this->adminAuthLogin->open();
            $this->adminAuthLogin->getLoginBlock()->fill($customAdmin);
            $this->adminAuthLogin->getLoginBlock()->submit();
        }
        $this->userRoleIndex->open();
        $this->userRoleIndex->getRoleGrid()->searchAndOpen($filter);
        if ($role->getRolesUsers() != null ) {
            $this->userRoleEditRole->getRoleForm()->fillRole($role, $userWithOutRole->getUsername());
        } else {
            $this->userRoleEditRole->getRoleForm()->fill($role);
        }
        $this->userRoleEditRole->getPageActions()->save();
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
