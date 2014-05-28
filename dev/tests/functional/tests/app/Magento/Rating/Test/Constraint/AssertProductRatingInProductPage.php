<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rating\Test\Constraint; 

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Rating\Test\Fixture\Rating;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductRatingInProductPage
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
     * Assert that product rating is displayed on frontend on product review
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductSimple $product
     * @param Rating $productRating
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductSimple $product,
        Rating $productRating
    ) {
        $catalogProductView->init($product);
        $catalogProductView->open();
        $catalogProductView->getReviewSummaryBlock()->getAddReviewLink()->click();

        $reviewForm = $catalogProductView->getReviewFormBlock();
        $ratingCode = $productRating->getRatingCode();
        \PHPUnit_Framework_Assert::assertTrue(
            $reviewForm->getRating($ratingCode)->isVisible(),
            'Product rating "' . $ratingCode . '" is not displayed.'
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
