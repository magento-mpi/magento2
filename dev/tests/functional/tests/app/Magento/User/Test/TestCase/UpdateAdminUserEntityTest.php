<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\TestCase;

use Magento\Backend\Test\Page\AdminAuthLogin;
use Magento\User\Test\Fixture\AdminUserInjectable;
use Magento\User\Test\Fixture\AdminUserRole;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;
use Magento\Backend\Test\Page\Dashboard;
use Magento\User\Test\Page\Adminhtml\UserEdit;
use Magento\User\Test\Page\Adminhtml\UserIndex;

/**
 * Test Creation for UpdateAdminUserEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Admin user with assigned full access role is created.
 * 2. Custom role with restricted permission: Sales is created
 *
 * Steps:
 * 1. Log in as admin user from data set
 * 2. Navigate to  System>Permissions>All Users
 * 3. Open user from precondition.
 * 4. Fill in all data according to data set
 * 5. Save user
 * 6. Perform all assertions
 *
 * @group ACL_(MX)
 * @ZephyrId MAGETWO-24345
 */
class UpdateAdminUserEntityTest extends Injectable
{
    /**
     * @var UserIndex
     */
    protected $userIndex;

    /**
     * @var UserEdit
     */
    protected $userEdit;

    /**
     * @var Dashboard
     */
    protected $dashboard;

    /**
     * @var AdminAuthLogin
     */
    protected $adminAuth;

    /**
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Setup necessary data for test
     *
     * @param UserIndex $userIndex
     * @param UserEdit $userEdit
     * @param Dashboard $dashboard
     * @param AdminAuthLogin $adminAuth
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __inject(
        UserIndex $userIndex,
        UserEdit $userEdit,
        Dashboard $dashboard,
        AdminAuthLogin $adminAuth,
        FixtureFactory $fixtureFactory
    ) {
        $this->userIndex = $userIndex;
        $this->userEdit = $userEdit;
        $this->dashboard = $dashboard;
        $this->adminAuth = $adminAuth;
        $this->fixtureFactory = $fixtureFactory;

        $initialUser = $this->fixtureFactory->createByCode(
            'adminUserInjectable',
            ['dataSet' => 'custom_admin_with_default_role']
        );
        $initialUser->persist();

        return ['initialUser' => $initialUser];
    }

    /**
     * Runs Update Admin User test
     *
     * @param AdminUserInjectable $user
     * @param AdminUserInjectable $initialUser
     * @param string $loginAsDefaultAdmin
     * @return array
     */
    public function testUpdateAdminUser(
        AdminUserInjectable $user,
        AdminUserInjectable $initialUser,
        $loginAsDefaultAdmin
    ) {
        // Prepare data
        $filter = ['username' => $initialUser->getUsername()];

        // Steps
        if ($loginAsDefaultAdmin == '0') {
            $this->adminAuth->open();
            $this->adminAuth->getLoginBlock()->fill($initialUser);
            $this->adminAuth->getLoginBlock()->submit();
        }
        $this->userIndex->open();
        $this->userIndex->getUserGrid()->searchAndOpen($filter);
        $this->userEdit->getUserForm()->fill($user);
        $this->userEdit->getPageActions()->save();
        $customAdmin = $this->mergeUsers($user, $initialUser);

        return ['customAdmin' => $customAdmin];
    }

    /**
     * Merging user data and returns custom user
     *
     * @param AdminUserInjectable $user
     * @param AdminUserInjectable $initialUser
     * @return AdminUserInjectable
     */
    protected function mergeUsers(
        AdminUserInjectable $user,
        AdminUserInjectable $initialUser
    ) {
        $data = array_merge($initialUser->getData(), $user->getData());
        if (isset($data['role_id'])) {
            $data['role_id'] = [
                'role' => ($user->hasData('role_id'))
                        ? $user->getDataFieldConfig('role_id')['source']->getRole()
                        : $initialUser->getDataFieldConfig('role_id')['source']->getRole()
            ];
        }
        $customAdmin = $this->fixtureFactory->createByCode('adminUserInjectable', ['data' => $data]);

        return $customAdmin;
    }

    /**
     * Logout Admin User from account
     *
     * @return void
     */
    public function tearDown()
    {
        if ($this->dashboard->getAdminPanelHeader()->isVisible()) {
            $this->dashboard->getAdminPanelHeader()->logOut();
        }
    }
}
