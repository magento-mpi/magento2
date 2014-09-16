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
 * Class AssertProductRatingNotInProductPage
 */
class AssertProductRatingNotInProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that product rating is not displayed on frontend on product review
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductSimple $product
     * @param Rating $productRating
     * @param Browser $browser
     * @param ReviewInjectable $review
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductSimple $product,
        Rating $productRating,
        Browser $browser,
        ReviewInjectable $review = null
    ) {
        $product = $review === null ? $product : $review->getDataFieldConfig('entity_id')['source']->getEntity();
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getReviewSummary()->getAddReviewLink()->click();

        $reviewForm = $catalogProductView->getReviewFormBlock();
        \PHPUnit_Framework_Assert::assertFalse(
            $reviewForm->isVisibleRating($productRating),
            'Product rating "' . $productRating->getRatingCode() . '" is displayed.'
        );
    }

    /**
     * Text success product rating is not displayed
     *
     * @return string
     */
    public function toString()
    {
        return 'Product rating is not displayed.';
    }
}
