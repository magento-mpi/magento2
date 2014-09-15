<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Constraint\AssertProductTierPriceOnProductPage;

/**
 * Class AssertTierPriceOnBundleProductPage
 */
class AssertTierPriceOnBundleProductPage extends AssertProductTierPriceOnProductPage
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
    protected $tierBlock = '.prices.tier.items';

    /**
     * Decimals for price format
     *
     * @var int
     */
    protected $priceFormat = 4;

    /**
     * Assertion that tier prices are displayed correctly
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @param Browser $browser
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, FixtureInterface $product, Browser $browser)
    {
        //Open product view page
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $viewBlock = $catalogProductView->getViewBlock();
        $viewBlock->clickCustomize();

        //Process assertions
        $this->assertPrice($product, $catalogProductView);
    }
}
