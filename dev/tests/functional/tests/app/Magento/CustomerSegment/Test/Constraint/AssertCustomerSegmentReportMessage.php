<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\CustomerSegment\Test\Fixture\CustomerSegment;
use Magento\CustomerSegment\Test\Page\Adminhtml\CustomerSegmentReportDetail;

/**
 * Class AssertCustomerSegmentReportMessage
 * Assert that message is displayed on the customer segment report detail page
 */
class AssertCustomerSegmentReportMessage extends AbstractConstraint
{
    /**
     * Customer segments report messages
     */
    const REPORT_MESSAGES = 'Viewing combined "%s" report from segments: %s.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that message is displayed on the customer segment report detail page
     *
     * @param CustomerSegmentReportDetail $reportPage
     * @param array $customerSegments
     * @param array $reportActions
     * @return void
     */
    public function processAssert(
        CustomerSegmentReportDetail $reportPage,
        array $customerSegments,
        array $reportActions
    ) {
        $names = [];
        foreach ($customerSegments as $customerSegment) {
            /** @var CustomerSegment $customerSegment */
            $names[] = $customerSegment->getName();
        }
        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::REPORT_MESSAGES, reset($reportActions['massaction']), implode(', ', $names)),
            $reportPage->getMessagesBlock()->getNoticeMessages(),
            'Wrong customer segment report message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer segment report message is displayed correctly.';
    }
}
