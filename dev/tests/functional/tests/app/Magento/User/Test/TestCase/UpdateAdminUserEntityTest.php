<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\TestCase;

use Magento\Backend\Test\Page\AdminAuthLogin;
use Magento\Cms\Test\Page\CmsIndex;
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
     * @var CmsIndex
     */
    protected $cmsIndex;

    /**
     * Run preconditions for test.
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $roleSales = $fixtureFactory->createByCode('adminUserRole', ['dataSet' => 'role_sales']);
        $roleSales->persist();
        return ['roleSales' => $roleSales];
    }

    /**
     * Setup page for test
     *
     * @param UserIndex $userIndex
     * @param UserEdit $userEdit
     * @param Dashboard $dashboard
     * @param AdminAuthLogin $adminAuth
     * @param FixtureFactory $fixtureFactory
     * @param AdminUserRole $defaultRole
     * @param CmsIndex $cmsIndex
     * @return array
     */
    public function __inject(
        UserIndex $userIndex,
        UserEdit $userEdit,
        Dashboard $dashboard,
        AdminAuthLogin $adminAuth,
        FixtureFactory $fixtureFactory,
        CmsIndex $cmsIndex
    ) {
        $this->userIndex = $userIndex;
        $this->userEdit = $userEdit;
        $this->dashboard = $dashboard;
        $this->adminAuth = $adminAuth;
        $this->cmsIndex = $cmsIndex;

        $customAdmin = $fixtureFactory->createByCode(
            'adminUserInjectable',
            ['dataSet' => 'custom_admin_with_default_role']
        );
        $customAdmin->persist();

        return [
            'customAdmin' => $customAdmin
        ];
    }

    /**
     * Runs Update Admin User test
     *
     * @param AdminUserInjectable $user
     * @param AdminUserInjectable $customAdmin
     * @param AdminUserRole $roleSales
     * @param string $useSalesRoleFromDataSet
     * @param string $loginAsDefaultAdmin
     * @return void
     */
    public function testUpdateAdminUser(
        AdminUserInjectable $user,
        AdminUserInjectable $customAdmin,
        AdminUserRole $roleSales,
        $useSalesRoleFromDataSet,
        $loginAsDefaultAdmin
    ) {
        // Prepare data
        $filter = ['username' => $customAdmin->getUsername()];
        $userRole = $useSalesRoleFromDataSet != '-' ? $roleSales : null;

        // Steps
        if ($loginAsDefaultAdmin == '0') {
            $this->adminAuth->open();
            $this->adminAuth->getLoginBlock()->fill($customAdmin);
            $this->adminAuth->getLoginBlock()->submit();
        }
        $this->userIndex->open();
        $this->userIndex->getUserGrid()->searchAndOpen($filter);
        $this->userEdit->getUserForm()->fillUser($user, $userRole);
        $this->userEdit->getPageActions()->save();
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
