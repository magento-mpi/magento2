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
use Magento\Catalog\Test\Constraint\AssertGroupedPriceOnProductPage;
use Magento\GroupedProduct\Test\Constraint\AssertPriceOnGroupedProductPage;

/**
 * Class AssertGroupedPriceOnGroupedProductPage
 */
class AssertGroupedPriceOnGroupedProductPage extends AssertGroupedPriceOnProductPage
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
    public $formatErrMessage = 'This "%s" product\'s grouped price on product page NOT equals passed from fixture.';

    /**
     * Assert that displayed grouped price on grouped product page equals passed from fixture
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, FixtureInterface $product)
    {
        $assertPrice = new AssertPriceOnGroupedProductPage;
        $assertPrice->assertPrice($product, $catalogProductView, $this, 'Grouped');
    }
}
