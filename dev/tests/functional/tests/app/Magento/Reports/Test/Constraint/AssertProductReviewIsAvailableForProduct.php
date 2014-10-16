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
use Magento\Review\Test\Page\Adminhtml\ReviewIndex;
use Magento\Reports\Test\Page\Adminhtml\ProductReportReview;
use Magento\Review\Test\Constraint\AssertProductReviewInGrid;

/**
 * Class AssertProductReviewIsVisibleInGrid
 * Assert that review is visible in review grid for select product
 */
class AssertProductReviewIsAvailableForProduct extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that review is visible in review grid for select product
     *
     * @param ReviewIndex $reviewIndex
     * @param ReviewInjectable $review
     * @param ProductReportReview $productReportReview
     * @param AssertProductReviewInGrid $assertProductReviewInGrid
     * @return void
     */
    public function processAssert(
        ReviewIndex $reviewIndex,
        ReviewInjectable $review,
        ProductReportReview $productReportReview,
        AssertProductReviewInGrid $assertProductReviewInGrid
    ) {
        $productReportReview->open();
        $product = $review->getDataFieldConfig('entity_id')['source']->getEntity();
        $productReportReview->getGridBlock()->openReview($product->getName());
        unset($assertProductReviewInGrid->filter['visible_in']);
        $filter = $assertProductReviewInGrid->prepareFilter($product, $review->getData(), '');
        \PHPUnit_Framework_Assert::assertTrue(
            $reviewIndex->getReviewGrid()->isRowVisible($filter, false),
            'Review for ' . $product->getName() . ' product is not visible in reports grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Review is visible in review grid for select product.';
    }
}
