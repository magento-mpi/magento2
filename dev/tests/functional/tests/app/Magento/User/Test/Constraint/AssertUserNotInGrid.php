<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\User\Test\Fixture\AdminUserInjectable;
use Magento\User\Test\Page\Adminhtml\UserIndex;

/**
 * Class AssertUserNotInGrid
 */
class AssertUserNotInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Asserts that User is not present in User Grid.
     *
     * @param UserIndex $userIndex
     * @param AdminUserInjectable $adminUser
     * @return void
     */
    public function processAssert(
        UserIndex $userIndex,
        AdminUserInjectable $adminUser
    ) {
        $filter = ['username' => $adminUser->getUsername()];
        $userIndex->open();
        \PHPUnit_Framework_Assert::assertFalse(
            $userIndex->getUserGrid()->isRowVisible($filter),
            'User with name \'' . $adminUser->getUsername() . '\' is present in Users grid.'
        );
    }

    /**
     * Returns message if user not in grid.
     *
     * @return string
     */
    public function toString()
    {
        return 'User is absent in Users grid.';
    }
}
