<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\CurrencySymbol\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\CurrencySymbol\Test\Fixture\CurrencySymbolEntity;
use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;

/**
 * Check that after applying changes, currency symbol changed on Product Details Page.
 */
class AssertCurrencySymbolOnProductPage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that after applying changes, currency symbol changed on Product Details Page.
     *
     * @param CatalogProductSimple $product
     * @param Browser $browser
     * @param CmsIndex $cmsIndex
     * @param CatalogProductView $catalogProductView
     * @param CurrencySymbolEntity $currencySymbol
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product,
        Browser $browser,
        CmsIndex $cmsIndex,
        CatalogProductView $catalogProductView,
        CurrencySymbolEntity $currencySymbol
    ) {
        $cmsIndex->open();
        $cmsIndex->getCurrencyBlock()->switchCurrency($currencySymbol);
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $price = $catalogProductView->getViewBlock()->getPriceBlock()->getPrice();
        preg_match('`(.*?)\d`', $price, $matches);

        $symbolOnPage = isset($matches[1]) ? $matches[1] : null;
        \PHPUnit_Framework_Assert::assertEquals(
            $currencySymbol->getCustomCurrencySymbol(),
            $symbolOnPage,
            'Wrong Currency Symbol is displayed on Product page.'
        );
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "Currency Symbol has been changed on Product Details page.";
    }
}
