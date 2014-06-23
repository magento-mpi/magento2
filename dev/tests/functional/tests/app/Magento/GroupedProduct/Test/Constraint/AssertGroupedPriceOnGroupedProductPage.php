<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Constraint;

use Magento\GroupedProduct\Test\Fixture\CatalogProductGrouped;
use Magento\GroupedProduct\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Constraint\AssertGroupedPriceOnProductPage;

/**
 * Class AssertGroupedPriceOnGroupedProductPage
 */
class AssertGroupedPriceOnGroupedProductPage extends AssertPriceOnGroupedProductPageAbstract
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Format error message
     *
     * @var string
     */
    protected $errorMessage = 'This "%s" product\'s grouped price on product page NOT equals passed from fixture.';

    /**
     * Successful message
     *
     * @var string
     */
    protected $successfulMessage = 'That displayed grouped price on grouped product page equals passed from fixture.';

    /**
     * Assert that displayed grouped price on grouped product page equals passed from fixture
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductGrouped $product
     * @param AssertGroupedPriceOnProductPage $groupedPrice
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductGrouped $product,
        AssertGroupedPriceOnProductPage $groupedPrice
    ) {
        $this->processAssertPrice($product, $catalogProductView, $groupedPrice);
    }
}
