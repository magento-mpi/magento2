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
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertProductReviewNotOnProductPage
 * Assert that product review Not available on product page
 */
class AssertProductReviewNotOnProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that product review Not available on product page
     *
     * @param CatalogProductView $catalogProductView
     * @param ReviewInjectable $reviewInitial
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, ReviewInjectable $reviewInitial)
    {
        /** @var CatalogProductSimple $product */
        $product = $reviewInitial->getDataFieldConfig('entity_id')['source']->getEntity();
        $catalogProductView->init($product);
        $catalogProductView->open();

        $reviewBlock = $catalogProductView->getCustomerReviewBlock();
        $catalogProductView->getViewBlock()->selectTab('Reviews');
        \PHPUnit_Framework_Assert::assertFalse(
            $reviewBlock->isVisibleReviewItem(),
            'Error, product review is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Review is not available on the product page.';
    }
}
