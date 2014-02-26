<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\User\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Backend\Test\Page\AdminAuthLogin;

/**
 * Class InvalidCredentials
 *
 * @package Magento\Backend\Test\Constraint
 */
class InvalidCredentials extends AbstractConstraint
{
    const INVALID_CREDENTIALS_MESSAGE = 'Please correct the user name or password.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Verify incorrect credentials message while login to admin
     *
     * @param AdminAuthLogin $loginPage
     */
    public function processAssert(AdminAuthLogin $loginPage)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::INVALID_CREDENTIALS_MESSAGE,
            $loginPage->getMessagesBlock()->getErrorMessages(),
            'Message \'' . self::INVALID_CREDENTIALS_MESSAGE . '\' is not visible.'
        );
    }

    /**
     * @inheritdoc
     */
    public function toString()
    {
        return 'Invalid credentials message was displayed.';
    }
}
