<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Page\Adminhtml\CustomerGroupIndex;

/**
 * Class AssertCustomerGroupSuccessDeleteMessage
 */
class AssertCustomerGroupSuccessDeleteMessage extends AbstractConstraint
{
    const SUCCESS_DELETE_MESSAGE= "The customer group has been deleted.";

    /* tags */
     const SEVERITY = 'high';
     /* end tags */

    /**
     * Assert that message "The customer group has been deleted." is displayed on Customer Group page.
     *
     * @param CustomerGroupIndex $customerGroupIndex
     * @return void
     */
    public function processAssert(CustomerGroupIndex $customerGroupIndex)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_DELETE_MESSAGE,
            $customerGroupIndex->getMessagesBlock()->getSuccessMessages(),
            'Wrong message is displayed.'
            . "\nExpected: " . self::SUCCESS_DELETE_MESSAGE
            . "\nActual: " . $customerGroupIndex->getMessagesBlock()->getSuccessMessages()
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
