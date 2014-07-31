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
 * Class AssertCustomerSegmentSuccessDeleteMessage
 * Assert that success delete message is displayed after Customer Segments deleted
 */
class AssertCustomerSegmentSuccessDeleteMessage extends AbstractConstraint
{
    /**
     * Success delete message
     */
    const SUCCESS_DELETE_MESSAGE = 'You deleted the segment.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success delete message is displayed after Customer Segments deleted
     *
     * @param CustomerSegmentIndex $customerSegmentIndex
     * @return void
     */
    public function processAssert(CustomerSegmentIndex $customerSegmentIndex)
    {
        $actualMessage = $customerSegmentIndex->getMessagesBlock()->getSuccessMessages();

        \PHPUnit_Framework_Assert::assertContains(
            self::SUCCESS_DELETE_MESSAGE,
            $actualMessage,
            'Wrong success delete message is displayed.'
            . "\nExpected: " . self::SUCCESS_DELETE_MESSAGE
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
        return 'Assert that success delete message is displayed';
    }
}
