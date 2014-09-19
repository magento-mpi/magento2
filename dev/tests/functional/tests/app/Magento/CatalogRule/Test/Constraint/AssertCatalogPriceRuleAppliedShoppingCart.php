<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\Checkout\Test\Page\CheckoutCart;

/**
 * Class AssertCatalogPriceRuleAppliedShoppingCart
 */
class AssertCatalogPriceRuleAppliedShoppingCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that Catalog Price Rule is applied for product(s) in Shopping Cart
     * according to Priority(Priority/Stop Further Rules Processing)
     *
     * @param CatalogProductSimple $product
     * @param CatalogProductView $pageCatalogProductView
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CheckoutCart $pageCheckoutCart
     * @param array $price
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product,
        CatalogProductView $pageCatalogProductView,
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CheckoutCart $pageCheckoutCart,
        array $price
    ) {
        $cmsIndex->open();
        $categoryName = $product->getCategoryIds()[0];
        $productName = $product->getName();
        $cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
        $catalogCategoryView->getListProductBlock()->openProductViewPage($productName);
        $pageCatalogProductView->getViewBlock()->clickAddToCartButton();
        $actualGrandTotal = $pageCheckoutCart->getCartBlock()->getCartItem($product)->getPrice();
        \PHPUnit_Framework_Assert::assertEquals(
            $price['grand_total'],
            $actualGrandTotal,
            'Wrong grand total price is displayed.'
            . "\nExpected: " . $price['grand_total']
            . "\nActual: " . $actualGrandTotal
        );
    }

    /**
     * Text of catalog price rule visibility in Shopping Cart (frontend)
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed catalog price rule data in shopping cart(frontend) equals to passed from fixture.';
    }
}
