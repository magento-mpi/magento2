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
 * Class AssertCustomerGroupSuccessSaveMessage
 */
class AssertCustomerGroupSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'The customer group has been saved.';

    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that success message is displayed after customer group save
     *
     * @param CustomerGroupIndex $customerGroupIndex
     * @return void
     */
    public function processAssert(CustomerGroupIndex $customerGroupIndex)
    {
        $actualMessage = $customerGroupIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Success assert of created customer group success message.
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer group success save message is present.';
    }
}
