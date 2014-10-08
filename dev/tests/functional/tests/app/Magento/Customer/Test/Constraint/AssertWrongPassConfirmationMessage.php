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
 * Class AssertWrongPassConfirmationMessage
 * Check that error message appears
 */
class AssertWrongPassConfirmationMessage extends AbstractConstraint
{
    const ERROR_MESSAGE = 'Confirm your new password';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert error message appears
     *
     * @param CustomerAccountEdit $customerAccountEdit
     * @return void
     */
    public function processAssert(CustomerAccountEdit $customerAccountEdit)
    {
        $actualMessage = $customerAccountEdit->getMessages()->getErrorMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::ERROR_MESSAGE,
            $actualMessage,
            'Wrong error message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Error message is displayed.';
    }
}
