<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Constraint;

use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Constraint\AbstractConstraint;
use Magento\User\Test\Page\Adminhtml\UserRoleIndex;
use Magento\User\Test\Fixture\AdminUserRole;
use Magento\User\Test\Fixture\AdminUserInjectable;
use Magento\Backend\Test\Page\AdminAuthLogin;

/**
 * Class AssertRoleInGrid
 */
class AssertRoleInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Asserts that saved role is present in Role Grid.
     *
     * @param UserRoleIndex $rolePage
     * @param AdminAuthLogin $adminAuthLogin
     * @param Browser $browser
     * @param AdminUserInjectable $customAdmin
     * @param AdminUserRole $role
     * @param AdminUserRole $roleInit
     * @return void
     */
    public function processAssert(
        UserRoleIndex $rolePage,
        AdminAuthLogin $adminAuthLogin,
        Browser $browser,
        AdminUserInjectable $customAdmin,
        AdminUserRole $role,
        AdminUserRole $roleInit = null
    ) {
        $filter = ['rolename' => $role->getRoleName() != null ? $role->getRoleName() : $roleInit->getRoleName()];
        if ($role->getRolesUsers() == null) {
            $browser->reopen(); // TODO Remove this after resolving bug in UpdateAdminUserRole test
            $adminAuthLogin->open();
            $adminAuthLogin->getLoginBlock()->fill($customAdmin);
            $adminAuthLogin->getLoginBlock()->submit();
        }
        $rolePage->open();
        $rolePage->getRoleGrid()->resetFilter();
        \PHPUnit_Framework_Assert::assertTrue(
            $rolePage->getRoleGrid()->isRowVisible($filter),
            'Role with name \'' . $role->getRoleName() . '\' is absent in Roles grid.'
        );
    }

    /**
     * Returns success message if assert true.
     *
     * @return string
     */
    public function toString()
    {
        return 'Role is present in Roles grid.';
    }
}
