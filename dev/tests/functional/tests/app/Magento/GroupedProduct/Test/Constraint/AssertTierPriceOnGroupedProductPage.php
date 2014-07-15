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
use Magento\Catalog\Test\Constraint\AssertTierPriceOnProductPage;

/**
 * Class AssertTierPriceOnGroupedProductPage
 */
class AssertTierPriceOnGroupedProductPage extends AbstractAssertPriceOnGroupedProductPage
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
    protected $errorMessage = 'For "%s" Product tier price on product page is not correct.';

    /**
     * Successful message
     *
     * @var string
     */
    protected $successfulMessage = 'Tier price is displayed on the grouped product page.';

    /**
     * Assert that displayed grouped price on grouped product page equals passed from fixture
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductGrouped $product
     * @param AssertTierPriceOnProductPage $tierPrice
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductGrouped $product,
        AssertTierPriceOnProductPage $tierPrice
    ) {
        $this->processAssertPrice($product, $catalogProductView, $tierPrice, 'Tier');
    }
}
