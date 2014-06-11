<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Block\Product\View;

/**
 * Class AssertTierPriceOnProductPage
 */
class AssertTierPriceOnProductPage extends AbstractConstraint
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
     * Error message
     *
     * @var string
     */
    public $errMessage = 'Product tier price on product page is not correct.';

    /**
     * Format price
     *
     * @var int
     */
    protected $priceFormat = 2;

    /**
     * Assertion that tier prices are displayed correctly
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        FixtureInterface $product
    ) {
        // TODO fix initialization url for frontend page
        //Open product view page
        $catalogProductView->init($product);
        $catalogProductView->open();

        //Process assertions
        $this->assertTierPrice($product, $catalogProductView);
    }

    /**
     * Verify product tier price on product view page
     *
     * @param FixtureInterface $product
     * @param CatalogProductView $catalogProductView
     * @param string $block
     * @return void
     */
    public function assertTierPrice(FixtureInterface $product, CatalogProductView $catalogProductView, $block = 'View')
    {
        $noError = true;
        $match = [];
        $index = 1;
        /** @var View $viewBlock */
        $viewBlock = $catalogProductView->{'get' . $block . 'Block'}();
        $tierPrices = $product->getTierPrice();

        if (isset($product->getData()['price_type'])) {
            $viewBlock->clickCustomize();
            $viewBlock->waitForElementVisible($this->tierBlock);
        }

        foreach ($tierPrices as $tierPrice) {
            $text = $viewBlock->getTierPrices($index++);
            $noError = (bool)preg_match('#^[^\d]+(\d+)[^\d]+(\d+(?:(?:,\d+)*)+(?:.\d+)*).*#i', $text, $match);
            if (!$noError) {
                break;
            }
            if (count($match) < 2
                && $match[1] != $tierPrice['price_qty']
                || $match[2] !== number_format($tierPrice['price'], $this->priceFormat)
            ) {
                $noError = false;
                break;
            }
        }

        \PHPUnit_Framework_Assert::assertTrue($noError, $this->errMessage);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Tier price is displayed on the product page.';
    }
}
