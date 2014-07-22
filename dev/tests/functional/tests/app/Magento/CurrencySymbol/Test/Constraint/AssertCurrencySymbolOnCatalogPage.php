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
use Magento\Catalog\Test\Page\Category\CatalogCategoryView;
use Magento\CurrencySymbol\Test\Fixture\CurrencySymbolEntity;

/**
 * Class AssertCurrencySymbolOnCatalogPage
 */
class AssertCurrencySymbolOnCatalogPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that after applying changes, currency symbol changed on Catalog page
     *
     * @param CmsIndex $cmsIndex
     * @param CatalogCategoryView $catalogCategoryView
     * @param CatalogProductSimple $product
     * @param CurrencySymbolEntity $currencySymbol
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CatalogCategoryView $catalogCategoryView,
        CatalogProductSimple $product,
        CurrencySymbolEntity $currencySymbol
    ) {
        $categoryName = $product->getCategoryIds()[0]['name'];
        $cmsIndex->open();
        $cmsIndex->getTopmenu()->selectCategoryByName($categoryName);
        $price = $catalogCategoryView->getListProductBlock()->getPrice($product->getId());
        preg_match('`(.*?)\d`', $price, $matches);

        \PHPUnit_Framework_Assert::assertEquals(
            $currencySymbol->getCustomCurrencySymbol(),
            $matches[1],
            'Wrong Currency Symbol is displayed on Category page.'
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
        return "Currency Symbol changed on Catalog page.";
    }
}
