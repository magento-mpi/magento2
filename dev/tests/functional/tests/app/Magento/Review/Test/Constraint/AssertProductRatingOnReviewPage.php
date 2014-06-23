<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Magento\Review\Test\Page\Adminhtml\ReviewEdit;
use Mtf\Constraint\AssertForm;

/**
 * Class AssertProductRatingOnReviewPage
 */
class AssertProductRatingOnReviewPage extends AssertForm
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
     * Assert that product rating is displayed on product review(backend)
     *
     * @param CatalogProductIndex $catalogProductIndex
     * @param CatalogProductEdit $catalogProductEdit
     * @param ReviewEdit $reviewEdit
     * @param CatalogProductSimple $product
     * @param ReviewInjectable $review
     * @retur void
     */
    public function processAssert(
        CatalogProductIndex $catalogProductIndex,
        CatalogProductEdit $catalogProductEdit,
        ReviewEdit $reviewEdit,
        CatalogProductSimple $product,
        ReviewInjectable $review
    ) {
        $productFilter = ['name' => $product->getName()];
        $reviewFilter = ['title' => $review->getTitle()];

        $catalogProductIndex->open();
        $catalogProductIndex->getProductGrid()->searchAndOpen($productFilter);
        $productForm = $catalogProductEdit->getForm();
        $productForm->openTab(self::TAB_REVIEWS);
        $productForm->getTabElement(self::TAB_REVIEWS)->getGrid()->searchAndOpen($reviewFilter);

        $reviewRatings = $review->getRatings();
        $formRatings = $reviewEdit->getReviewForm()->getRatings();
        $this->sortData($reviewRatings, ['::title']);
        $this->sortData($formRatings, ['::title']);
        $error = $this->verifyData($reviewRatings, $formRatings);
        \PHPUnit_Framework_Assert::assertTrue(null === $error, $error);
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
