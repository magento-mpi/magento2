<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Magento\Customer\Test\Page\CustomerAccountEdit;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertChangePasswordFailMessage
 * Check that fail message present
 */
class AssertChangePasswordFailMessage extends AbstractConstraint
{
    const FAIL_MESSAGE = "Password doesn't match for this account.";

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that fail message present
     *
     * @param CustomerAccountEdit $customerAccountEdit
     * @return void
     */
    public function processAssert(CustomerAccountEdit $customerAccountEdit)
    {
        $actualMessage = $customerAccountEdit->getMessages()->getErrorMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::FAIL_MESSAGE,
            $actualMessage,
            'Wrong fail message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Fail message is displayed.';
    }
}
