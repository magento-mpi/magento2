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
use Magento\User\Test\Page\Adminhtml\UserEdit;
use Magento\User\Test\Page\Adminhtml\UserIndex;
use Mtf\TestCase\Injectable;
use Magento\User\Test\Fixture\AdminUserInjectable;
use Mtf\Fixture\FixtureFactory;

/**
 * Test Creation for DeleteAdminUserEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create new admin user and assign it to new role.
 * Steps:
 * 1. Log in as admin user from data set.
 * 2. Go to System>Permissions>All Users
 * 3. Open admin user from precondition
 * 4. Click "Delete User" button
 * 5. Perform all assertions
 * @group ACL_(MX)
 * @ZephyrId MAGETWO-23416
 */
class DeleteAdminUserEntityTest extends Injectable
{
    /**
     * @var UserIndex $userIndex
     */
    protected $userIndex;

    /**
     * @var UserEdit $userEdit
     */
    protected $userEdit;

    /**
     * @var Dashboard $dashboard
     */
    protected $dashboard;

    /**
     * @var AdminAuthLogin AdminAuthLogin
     */
    protected $adminAuthLogin;

    /**
     * Preparing preconditions for test.
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
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
            'adminUser' => $adminUser
        ];
    }

    /**
     * @param UserIndex $userIndex
     * @param UserEdit $userEdit
     * @param Dashboard $dashboard
     * @param AdminAuthLogin $adminAuthLogin
     * @return array
     */
    public function __inject(
        UserIndex $userIndex,
        UserEdit $userEdit,
        Dashboard $dashboard,
        AdminAuthLogin $adminAuthLogin
    ) {
        $this->userIndex = $userIndex;
        $this->userEdit = $userEdit;
        $this->dashboard = $dashboard;
        $this->adminAuthLogin = $adminAuthLogin;
    }

    /**
     * Runs Delete User Entity test
     *
     * @param AdminUserInjectable $adminUser
     * @param string $isDefaultUser
     * @return void
     */
    public function testDeleteAdminUserEntity(
        AdminUserInjectable $adminUser,
        $isDefaultUser
    ) {
        $filter = [
            'username' => $adminUser->getUsername()
        ];
        //Steps
        if ($isDefaultUser == 0) {
            $this->adminAuthLogin->open();
            $this->adminAuthLogin->getLoginBlock()->fill($adminUser);
            $this->adminAuthLogin->getLoginBlock()->submit();
        }
        $this->userIndex->open();
        $this->userIndex->getUserGrid()->searchAndOpen($filter);
        $this->userEdit->getPageActions()->delete();
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
