<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertTierPriceOnProductPage
 *
 * @package Magento\Catalog\Test\Constraint
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
     * Process assert
     *
     * @param CatalogProductView $catalogProductView
     * @param CatalogProductSimple $product
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CatalogProductSimple $product
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
     * @param CatalogProductSimple $product
     * @param CatalogProductView $catalogProductView
     * @return void
     */
    protected function assertTierPrice(CatalogProductSimple $product, CatalogProductView $catalogProductView)
    {
        $noError = true;
        $match = [];
        $index = 1;
        $viewBlock = $catalogProductView->getViewBlock();
        $tierPrices = $product->getTierPrice();

        foreach ($tierPrices as $tierPrice) {
            $text = $viewBlock->getTierPrices($index++);
            $noError = (bool)preg_match('#^[^\d]+(\d+)[^\d]+(\d+(?:(?:,\d+)*)+(?:.\d+)*).*#i', $text, $match);
            if (!$noError) {
                break;
            }
            if ( count($match) < 2
                && $match[1] != $tierPrice['price_qty']
                || $match[2] !== number_format($tierPrice['price'], 2)
            ) {
                $noError = false;
                break;
            }
        }

        \PHPUnit_Framework_Assert::assertTrue($noError, 'Product tier price on product page is not correct.');
    }

    /**
     * Text of visible in tier price product assert
     *
     * @return string
     */
    public function toString()
    {
        return 'Tier price is displayed on the product page';
    }
}
