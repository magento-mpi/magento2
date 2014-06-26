<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Constraint;

use Magento\Review\Test\Page\Adminhtml\ReviewIndex;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductReviewInGrid
 */
class AssertProductReviewInGrid extends AbstractConstraint
{
    /**
     * Name of reviews tab on product edit page
     */
    const TAB_REVIEWS = 'product-reviews';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that review is displayed in grid on product reviews tab
     *
     * @param ReviewIndex $reviewIndex
     * @param ReviewInjectable $review
     * @return void
     */
    public function processAssert(ReviewIndex $reviewIndex, ReviewInjectable $review)
    {
        $filter = ['title' => $review->getTitle()];

        $reviewIndex->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $reviewIndex->getReviewGrid()->isRowVisible($filter),
            'Review with '
            . 'title "' . $filter['title'] . '"'
            . 'is absent in Review grid.'
        );
    }

    /**
     * Text success exist review in grid on product reviews tab
     *
     * @return string
     */
    public function toString()
    {
        return 'Review is present in grid on product reviews tab.';
    }
}
