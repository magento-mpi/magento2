<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Checkout\Test\Page\CheckoutCart;

/**
 * Class AssertCrossSellsProductsSection
 */
class AssertCrossSellsProductsSection extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that product is displayed in cross-sell section
     *
     * @param CatalogProductSimple $product1
     * @param CatalogProductSimple $product2
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product1,
        CatalogProductSimple $product2,
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart
    ) {
        $categoryIds = $product1->getCategoryIds();
        $category = reset($categoryIds);

        $checkoutCart->open();
        $checkoutCart->getCartBlock()->clearShoppingCart();
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($category['name']);
        $catalogCategoryView->getListProductBlock()->openProductViewPage($product1->getName());
        $catalogProductView->getViewBlock()->addToCart($product1);

        \PHPUnit_Framework_Assert::assertTrue(
            $checkoutCart->getCrosssellBlock()->verifyProductCrosssell($product2),
            'Product \'' . $product2->getName() . '\' is absent in cross-sell section.'
        );
    }

    /**
     * Text success product is displayed in cross-sell section
     *
     * @return string
     */
    public function toString()
    {
        return 'Assert that product is displayed in cross-sell section.';
    }
}
