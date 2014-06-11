<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\GroupedProduct\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Constraint\AssertTierPriceOnProductPage;
use Magento\GroupedProduct\Test\Constraint\AssertPriceOnGroupedProductPage;

/**
 * Class AssertTierPriceOnGroupedProductPage
 */
class AssertTierPriceOnGroupedProductPage extends AssertTierPriceOnProductPage
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
     * Error message
     *
     * @var string
     */
    public $formatErrMessage = 'For "%s" Product tier price on product page is not correct.';

    /**
     * Assertion that tier prices are displayed correctly
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, FixtureInterface $product)
    {
        $assertPrice = new AssertPriceOnGroupedProductPage;
        $assertPrice->assertPrice($product, $catalogProductView, $this, 'Tier');
    }
}
