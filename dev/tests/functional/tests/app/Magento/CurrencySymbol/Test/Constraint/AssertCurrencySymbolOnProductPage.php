<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CurrencySymbol\Test\Constraint;

use Mtf\Client\Browser;
use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\CurrencySymbol\Test\Fixture\CurrencySymbolEntity;

/**
 * Check that after applying changes, currency symbol changed on Product Details Page.
 */
class AssertCurrencySymbolOnProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

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
