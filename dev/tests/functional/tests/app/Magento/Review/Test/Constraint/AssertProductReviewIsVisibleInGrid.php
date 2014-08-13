<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Magento\Review\Test\Page\Adminhtml\ReviewIndex;
use Magento\Reports\Test\Page\Adminhtml\ProductReportReview;

/**
 * Class AssertProductReviewIsVisibleInGrid
 * Assert that review is visible in review grid
 */
class AssertProductReviewIsVisibleInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that review is visible in review grid
     *
     * @param ReviewIndex $reviewIndex
     * @param ReviewInjectable $review
     * @param ProductReportReview $productReportReview
     * @param AssertProductReviewInGrid $parentAssert
     * @return void
     */
    public function processAssert(
        ReviewIndex $reviewIndex,
        ReviewInjectable $review,
        ProductReportReview $productReportReview,
        AssertProductReviewInGrid $parentAssert
    ) {
        $productReportReview->open();
        $product = $review->getDataFieldConfig('entity_id')['source']->getEntity();
        $productReportReview->getGridBlock()->openReview($product->getName());
        unset($parentAssert->filter['visible_in']);
        $filter = $parentAssert->prepareFilter($product, $review, '');
        \PHPUnit_Framework_Assert::assertTrue(
            $reviewIndex->getReviewGrid()->isRowVisible($filter, false),
            'Review for ' . $product->getName() . 'is\'n visible in review grid'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'This review is visible in review grid.';
    }
}
