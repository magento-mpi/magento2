<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Constraint;

use Mtf\Client\Browser;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Magento\Review\Test\Fixture\Rating;
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
     * @param CatalogProductSimple $product
     * @param Browser $browser
     * @param ReviewInjectable|null $review [optional]
     * @param Rating|null $productRating [optional]
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductSimple $product,
        Browser $browser,
        ReviewInjectable $review = null,
        Rating $productRating = null
    ) {
        $product = $review === null ? $product : $review->getDataFieldConfig('entity_id')['source']->getEntity();
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
