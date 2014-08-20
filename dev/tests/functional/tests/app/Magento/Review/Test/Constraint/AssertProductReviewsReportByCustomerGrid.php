<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Constraint;

use Magento\Reports\Test\Page\Adminhtml\CustomerReportReview;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductReviewsReportByCustomerGrid
 */
class AssertProductReviewsReportByCustomerGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert product reviews qty column in Review Report by Customer grid
     *
     * @param CustomerReportReview $customerReportReview
     * @param string $customerName
     * @param int $reviewsCount
     * @return void
     */
    public function processAssert(CustomerReportReview $customerReportReview, $customerName, $reviewsCount)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            $reviewsCount,
            $customerReportReview->getGridBlock()->getQtyReview($customerName),
            'Wrong qty review in Customer Reviews Report grid.'
        );
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Product reviews qty column in Review Report by Customer grid is correctly.';
    }
}
