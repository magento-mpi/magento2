<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Magento\Reports\Test\Page\Adminhtml\ProductReportReview;

/**
 * Class AssertProductReviewReportIsVisibleInGrid
 * Assert that Product Review Report is visible in reports grid
 */
class AssertProductReviewReportIsVisibleInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that Product Review Report is visible in reports grid
     *
     * @param ProductReportReview $productReportReview
     * @param ReviewInjectable $review
     * @return void
     */
    public function processAssert(ProductReportReview $productReportReview, ReviewInjectable $review)
    {
        $productReportReview->open();
        $name = $review->getDataFieldConfig('entity_id')['source']->getEntity()->getName();
        \PHPUnit_Framework_Assert::assertTrue(
            $productReportReview->getGridBlock()->isRowVisible(['title' => $name], false),
            'Review for ' . $name . ' product is not visible in reports grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product Review Report is visible in reports grid.';
    }
}
