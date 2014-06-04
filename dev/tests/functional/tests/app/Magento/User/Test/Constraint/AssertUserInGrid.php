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
use Magento\User\Test\Page\Adminhtml\UserIndex;

/**
 * Class AssertUserInGrid
 */
class AssertUserInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Asserts that user is present in User Grid.
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
        \PHPUnit_Framework_Assert::assertTrue(
            $userIndex->getUserGrid()->isRowVisible($filter),
            'User with name \'' . $adminUser->getUsername() . '\' is absent in User grid.'
        );
    }

    /**
     * Returns success message if assert true.
     *
     * @return string
     */
    public function toString()
    {
        return 'User is present in Users grid.';
    }
}
