<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Backend\Test\Page\Dashboard;
use Magento\User\Test\Fixture\User;

/**
 * Class LoginSuccess
 */
class LoginSuccess extends AbstractConstraint
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
     * @param User $fixture
     * @param Dashboard $dashboard
     */
    public function processAssert(User $fixture, Dashboard $dashboard)
    {
        \PHPUnit_Framework_Assert::assertTrue(
            $dashboard->getAdminPanelHeader()->isLoggedIn(),
            'Admin user was not logged in.'
        );
    }

    /**
     * @inheritdoc
     */
    public function toString()
    {
        return 'Admin user is logged in.';
    }
}
