<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CurrencySymbol\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\CurrencySymbol\Test\Fixture\CurrencySymbolEntity;

/**
 * Class AssertCurrencySymbolOnPDP
 */
class AssertCurrencySymbolOnPDP extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that after applying changes, currency symbol changed on Product Details Page
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductSimple $product
     * @param CatalogProductView $catalogProductView
     * @param CurrencySymbolEntity $currencySymbol
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductSimple $product,
        CatalogProductView $catalogProductView,
        CurrencySymbolEntity $currencySymbol
    ) {
        $categoryName = $product->getCategoryIds()[0];
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
        $catalogCategoryView->getListProductBlock()->openProductViewPage($product->getName());
        $price = $catalogProductView->getViewBlock()->getProductPrice();
        preg_match('`(.*?)\d`', $price['price_regular_price'], $matches);

        \PHPUnit_Framework_Assert::assertEquals(
            $currencySymbol->getCustomCurrencySymbol(),
            $matches[1],
            'Wrong Currency Symbol is displayed on Product page.'
            . "\nExpected: " . $currencySymbol->getCustomCurrencySymbol()
            . "\nActual: " . $matches[1]
        );
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return "Currency Symbol changed on Product Details page.";
    }
}
