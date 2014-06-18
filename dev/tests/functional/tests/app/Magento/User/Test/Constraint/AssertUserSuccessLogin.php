<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Constraint;

use Magento\User\Test\Fixture\AdminUserInjectable;
use Mtf\Constraint\AbstractConstraint;
use Magento\Backend\Test\Page\Dashboard;
use Magento\Backend\Test\Page\AdminAuthLogin;
use Magento\User\Test\Fixture\AdminUserRole;

/**
 * Class AssertUserSuccessLogin
 */
class AssertUserSuccessLogin extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Verify whether customer has logged in to the Backend
     *
     * @param Dashboard $dashboard
     * @param AdminUserInjectable $user
     * @param AdminAuthLogin $adminAuth
     * @param AdminUserInjectable $customAdmin
     * @param string|null $userWithOutRole
     * @param string|null $userToLoginInAssert
     * @return void
     */
    public function processAssert(
        Dashboard $dashboard,
        AdminUserInjectable $user,
        AdminAuthLogin $adminAuth,
        AdminUserInjectable $customAdmin,
        $userWithOutRole = null,
        $userToLoginInAssert = null
    ) {
        $adminUser = $userToLoginInAssert != null ? $$userToLoginInAssert : $user;
        if ($dashboard->getAdminPanelHeader()->isVisible()) {
            $dashboard->getAdminPanelHeader()->logOut();
        }
        $adminAuth->getLoginBlock()->fill($adminUser);
        $adminAuth->getLoginBlock()->submit();

        \PHPUnit_Framework_Assert::assertTrue(
            $dashboard->getAdminPanelHeader()->isLoggedIn(),
            'Admin user was not logged in.'
        );
    }

    /**
     * Returns success message if equals to expected message
     *
     * @return string
     */
    public function toString()
    {
        return 'Admin user is logged in.';
    }
}
