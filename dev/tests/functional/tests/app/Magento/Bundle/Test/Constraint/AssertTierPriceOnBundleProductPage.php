<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Bundle\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Constraint\AssertTierPriceOnProductPage;

/**
 * Class AssertTierPriceOnBundleProductPage
 */
class AssertTierPriceOnBundleProductPage extends AssertTierPriceOnProductPage
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
    protected $decimals = 4;

    /**
     * Assertion that tier prices are displayed correctly
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, FixtureInterface $product)
    {
        // TODO fix initialization url for frontend page
        //Open product view page
        $catalogProductView->init($product);
        $catalogProductView->open();
        $viewBlock = $catalogProductView->getViewBlock();
        $viewBlock->clickCustomize();
        $viewBlock->waitForElementVisible($this->tierBlock);

        //Process assertions
        $this->assertTierPrice($product, $catalogProductView);
    }
}
