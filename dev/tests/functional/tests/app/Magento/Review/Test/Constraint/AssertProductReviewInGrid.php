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
     * @param CatalogProductIndex $catalogProductIndex
     * @param CatalogProductEdit $catalogProductEdit
     * @param CatalogProductSimple $product
     * @param ReviewInjectable $review
     * @return void
     */
    public function processAssert(
        CatalogProductIndex $catalogProductIndex,
        CatalogProductEdit $catalogProductEdit,
        CatalogProductSimple $product,
        ReviewInjectable $review
    ) {
        $productFilter = ['name' => $product->getName()];
        $reviewFilter = ['title' => $review->getTitle()];

        $catalogProductIndex->open();
        $catalogProductIndex->getProductGrid()->searchAndOpen($productFilter);
        $productForm = $catalogProductEdit->getForm();
        $productForm->openTab(self::TAB_REVIEWS);
        \PHPUnit_Framework_Assert::assertTrue(
            $productForm->getTabElement(self::TAB_REVIEWS)->getGrid()->isRowVisible($reviewFilter),
            'Review with '
            . 'title "' . $reviewFilter['title'] . '"'
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
