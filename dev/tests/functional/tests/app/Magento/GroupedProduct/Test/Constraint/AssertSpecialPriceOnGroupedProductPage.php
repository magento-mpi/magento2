<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Constraint;

use Mtf\Client\Browser;
use Magento\GroupedProduct\Test\Fixture\GroupedProductInjectable;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Constraint\AssertProductSpecialPriceOnProductPage;

/**
 * Class AssertSpecialPriceOnGroupedProductPage
 */
class AssertSpecialPriceOnGroupedProductPage extends AbstractAssertPriceOnGroupedProductPage
{
    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Format error message
     *
     * @var string
     */
    protected $errorMessage = 'This "%s" product\'s special price on product page NOT equals passed from fixture.';

    /**
     * Successful message
     *
     * @var string
     */
    protected $successfulMessage = 'Special price on grouped product page equals passed from fixture.';

    /**
     * Assert that displayed grouped price on grouped product page equals passed from fixture
     *
     * @param CatalogProductView $catalogProductView
     * @param GroupedProductInjectable $product
     * @param AssertProductSpecialPriceOnProductPage $specialPrice
     * @param Browser $browser
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        GroupedProductInjectable $product,
        AssertProductSpecialPriceOnProductPage $specialPrice,
        Browser $browser
    ) {
        $this->processAssertPrice($product, $catalogProductView, $specialPrice, $browser);
    }
}
