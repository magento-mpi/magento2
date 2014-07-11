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
 * Class AssertProductTierPriceOnProductPage
 */
class AssertTierPriceOnProductPage extends AbstractConstraint implements AssertPriceOnProductPageInterface
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Error message
     *
     * @var string
     */
    protected $errorMessage = 'Product tier price on product page is not correct.';

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
    public function processAssert(CatalogProductView $catalogProductView, FixtureInterface $product)
    {
        // TODO fix initialization url for frontend page
        //Open product view page
        $catalogProductView->init($product);
        $catalogProductView->open();

        //Process assertions
        $this->assertPrice($product, $catalogProductView);
    }

    /**
     * Set $errorMessage for tier price assert
     *
     * @param string $errorMessage
     * @return void
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * Verify product tier price on product view page
     *
     * @param FixtureInterface $product
     * @param CatalogProductView $catalogProductView
     * @param string $block [optional]
     * @return void
     */
    public function assertPrice(FixtureInterface $product, CatalogProductView $catalogProductView, $block = '')
    {
        $noError = true;
        $match = [];
        $index = 1;
        /** @var View $viewBlock */
        $viewBlock = $catalogProductView->{'get' . $block . 'ViewBlock'}();
        $tierPrices = $product->getTierPrice();

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

        \PHPUnit_Framework_Assert::assertTrue($noError, $this->errorMessage);
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
