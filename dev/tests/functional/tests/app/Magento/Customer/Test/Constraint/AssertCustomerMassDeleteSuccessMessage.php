<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerMassDeleteSuccessMessage
 * Check that message "A total of "x" record(s) were deleted." is present
 */
class AssertCustomerMassDeleteSuccessMessage extends AbstractConstraint
{
    /**
     * Message that appears after deletion via mass actions
     */
    const SUCCESS_DELETE_MESSAGE = 'A total of %d record(s) were deleted.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that message "A total of "x" record(s) were deleted."
     *
     * @param $customersQtyToDelete
     * @param CustomerIndex $customerIndexPage
     * @return void
     */
    public function processAssert($customersQtyToDelete, CustomerIndex $customerIndexPage)
    {
        $deleteMessage = sprintf(self::SUCCESS_DELETE_MESSAGE, $customersQtyToDelete);
        \PHPUnit_Framework_Assert::assertEquals(
            $deleteMessage,
            $customerIndexPage->getMessagesBlock()->getSuccessMessages(),
            'Wrong delete message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Mass delete customer message is displayed.';
    }
}
