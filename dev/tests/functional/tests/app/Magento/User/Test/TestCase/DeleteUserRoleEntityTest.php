<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\TestCase;

use Magento\User\Test\Fixture\AdminUserInjectable;
use Magento\User\Test\Fixture\AdminUserRole;
use Magento\Backend\Test\Page\AdminAuth;
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
     * @var AdminAuth
     */
    protected $adminAuth;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param UserRoleIndex $userRoleIndex
     * @param UserRoleEditRole $userRoleEditRole
     * @param AdminUserRole $role
     * @param AdminAuth $adminAuth
     * @return array
     */
    public function __inject(
        FixtureFactory $fixtureFactory,
        UserRoleIndex $userRoleIndex,
        UserRoleEditRole $userRoleEditRole,
        AdminAuth $adminAuth,
        AdminUserRole $role
    ) {
        $this->userRoleIndex = $userRoleIndex;
        $this->userRoleEditRole = $userRoleEditRole;
        $this->adminAuth = $adminAuth;

        $role = $fixtureFactory->createByCode('adminUserRole', ['dataSet' => 'default']);
        $role->persist();
        $role_id = $role->getData('role_id');
        $adminUserInjectable = $fixtureFactory->createByCode(
            'adminUserInjectable',
            [
                'dataSet' => 'custom_admin',
                'data' => ['role_id' => $role_id]
            ]
        );
        $adminUserInjectable->persist();

        return [
            'role' => $role,
            'adminUserInjectable' => $adminUserInjectable
        ];
    }

    /**
     * Runs Delete User Role Entity test.
     *
     * @param AdminUserRole $role
     * @param AdminUserInjectable $adminUserInjectable
     * $param string $isDefaultUser
     */
    public function testDeleteAdminUserRole(
        AdminUserRole $role,
        AdminUserInjectable $adminUserInjectable,
        $isDefaultUser
    ) {
        $filter = [
            'role_name' => $role->getData('role_name')
        ];
        //Steps
        if ($isDefaultUser == 0) {
            $this->adminAuth->open();
            $this->adminAuth->getLoginForm()->fill($adminUserInjectable);
            $this->adminAuth->getLoginForm()->submit();
            $this->userRoleIndex->open();
        } else {
            $this->userRoleIndex->open();
        }
        $this->userRoleIndex->getRoleGrid()->searchAndOpen($filter);
        $this->userRoleEditRole->getPageActions()->delete();
    }
}
