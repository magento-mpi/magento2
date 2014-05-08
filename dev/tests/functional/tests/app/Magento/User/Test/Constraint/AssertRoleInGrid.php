<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\User\Test\Page\Adminhtml\UserRoleIndex;
use Magento\User\Test\Fixture\AdminUserRole;

/**
 * Class AssertRoleInGrid
 *
 * @package Magento\User\Test\Constraint
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
     * @return void
     */
    public function processAssert(
        UserRoleIndex $rolePage,
        AdminUserRole $role
    ) {
        $filter = ['role_name' => $role->getRoleName()];
        $rolePage->open();
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
