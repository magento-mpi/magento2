<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;

/**
 * Class AssertCustomerSuccessDeleteMessage
 */
class AssertCustomerSuccessDeleteMessage extends AbstractConstraint
{
    const DELETE_MESSAGE = 'You deleted the customer.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Asserts that actual delete message equals expected
     *
     * @param CustomerIndex $customerIndexPage
     * @return void
     */
    public function processAssert(CustomerIndex $customerIndexPage)
    {
        $actualMessage = $customerIndexPage->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::DELETE_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::DELETE_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text success delete message is displayed
     *
     * @return string
     */
    public function toString()
    {
        return 'Assert that success delete message is displayed.';
    }
}
