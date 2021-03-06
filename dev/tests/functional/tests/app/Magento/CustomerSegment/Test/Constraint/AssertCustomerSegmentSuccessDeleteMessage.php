<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CustomerSegment\Test\Constraint;

use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerSegmentSuccessDeleteMessage
 * Assert that success delete message is displayed after Customer Segments deleted
 */
class AssertCustomerSegmentSuccessDeleteMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Success delete message
     */
    const SUCCESS_DELETE_MESSAGE = 'You deleted the segment.';

    /**
     * Assert that success delete message is displayed after Customer Segments has been deleted
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
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer Segments success delete message is displayed';
    }
}
