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
    protected $rolePage;

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
        $userWithOutRole = $fixtureFactory->createByCode('adminUserInjectable', ['dataSet' => 'custom_admin']);
        $userWithOutRole->persist();
        return ['userWithOutRole' => $userWithOutRole];
    }

    /**
     * Preconditions for test
     *
     * @param UserRoleIndex $rolePage
     * @param UserRoleEditRole $userRoleEditRole
     * @param AdminAuthLogin $adminAuthLogin
     * @param Dashboard $dashboard
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __inject(
        UserRoleIndex $rolePage,
        UserRoleEditRole $userRoleEditRole,
        AdminAuthLogin $adminAuthLogin,
        Dashboard $dashboard,
        FixtureFactory $fixtureFactory
    ) {
        $this->rolePage = $rolePage;
        $this->userRoleEditRole = $userRoleEditRole;
        $this->adminAuthLogin = $adminAuthLogin;
        $this->dashboard = $dashboard;

        $customAdmin = $fixtureFactory->createByCode(
            'adminUserInjectable',
            ['dataSet' => 'custom_admin_with_default_role']
        );
        $customAdmin->persist();

        return [
            'roleInit' => $customAdmin->getDataFieldConfig('role_id')['source']->getRole(),
            'customAdmin' => $customAdmin
        ];
    }

    /**
     * Runs Update Admin User Roles Entity test
     *
     * @param AdminUserRole $roleInit
     * @param AdminUserRole $role
     * @param AdminUserInjectable $customAdmin
     * @param AdminUserInjectable $user
     * @param AdminUserInjectable $userWithOutRole
     * @param string $userToLoginInAssert
     * @return void
     */
    public function testUpdateAdminUserRolesEntity(
        AdminUserRole $roleInit,
        AdminUserRole $role,
        AdminUserInjectable $customAdmin,
        AdminUserInjectable $user,
        AdminUserInjectable $userWithOutRole,
        $userToLoginInAssert
    ) {
        $filter = ['rolename' => $roleInit->getRoleName()];

        // Steps:
        if ($role->getRoleName() != null) {
            $this->adminAuthLogin->open();
            $this->adminAuthLogin->getLoginBlock()->fill($customAdmin);
            $this->adminAuthLogin->getLoginBlock()->submit();
        }
        $this->rolePage->open();
        $this->rolePage->getRoleGrid()->searchAndOpen($filter);
        $username = $role->getRolesUsers() != null ? $userWithOutRole->getUsername() : null;
        $this->userRoleEditRole->getRoleFormTabs()->fillRole($role, $username);
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
