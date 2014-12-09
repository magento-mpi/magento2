<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentIndex;

/**
 * Class AssertCustomerSegmentSuccessSaveMessage
 * Assert that success message is displayed after Customer Segments saved
 */
class AssertCustomerSegmentSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You saved the segment.';

    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that success message is displayed after Customer Segments saved
     *
     * @param CustomerSegmentIndex $customerSegmentIndex
     * @return void
     */
    public function processAssert(CustomerSegmentIndex $customerSegmentIndex)
    {
        $actualMessage = $customerSegmentIndex->getMessagesBlock()->getSuccessMessages();

        \PHPUnit_Framework_Assert::assertContains(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Assert that success message is displayed';
    }
}
