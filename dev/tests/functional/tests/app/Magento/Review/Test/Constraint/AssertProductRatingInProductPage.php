<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Review\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Review\Test\Fixture\Rating;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductRatingInProductPage
 * Assert that product rating is displayed on product review(frontend)
 */
class AssertProductRatingInProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that product rating is displayed on product review(frontend)
     *
     * @param CatalogProductView $catalogProductView
     * @param Browser $browser
     * @param CatalogProductSimple $product
     * @param ReviewInjectable|null $review [optional]
     * @param Rating|null $productRating [optional]
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        Browser $browser,
        CatalogProductSimple $product,
        ReviewInjectable $review = null,
        Rating $productRating = null
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $reviewSummaryBlock = $catalogProductView->getReviewSummary();
        if ($reviewSummaryBlock->isVisible()) {
            $reviewSummaryBlock->getAddReviewLink()->click();
        }
        $rating = $productRating ? $productRating : $review->getDataFieldConfig('ratings')['source']->getRatings()[0];
        $reviewForm = $catalogProductView->getReviewFormBlock();
        \PHPUnit_Framework_Assert::assertTrue(
            $reviewForm->isVisibleRating($rating),
            'Product rating "' . $rating->getRatingCode() . '" is not displayed.'
        );
    }

    /**
     * Text success product rating is displayed
     *
     * @return string
     */
    public function toString()
    {
        return 'Product rating is displayed.';
    }
}
