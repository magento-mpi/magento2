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
class AssertTierPriceOnGroupedProductPage extends AssertPriceOnGroupedProductPageAbstract
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Tier price block
     *
     * @var string
     */
    protected $tierBlock = '#super-product-table tr:nth-child(%d) .prices.tier.items';

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
     * @param AssertTierPriceOnProductPage $specialPrice
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductGrouped $product,
        AssertTierPriceOnProductPage $specialPrice
    ) {
        $this->processAssertPrice($product, $catalogProductView, $specialPrice, 'Tier');
    }
}
