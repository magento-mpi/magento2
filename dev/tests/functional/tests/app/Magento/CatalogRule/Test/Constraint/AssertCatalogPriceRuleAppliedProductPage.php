<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Test\Constraint; 

use Mtf\Constraint\AbstractConstraint;
use Magento\CatalogRule\Test\Fixture\CatalogRule;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertCatalogPriceRuleAppliedProductPage
 */
class AssertCatalogPriceRuleAppliedProductPage extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert that Catalog Price Rule is applied for product(s) on Product page according to Priority
     *
     * @param CatalogRule $catalogPriceRule
     * @param CatalogProductSimple $product
     * @param CatalogProductView $pageCatalogProductView
     * @return void
     */
    public function processAssert(
        CatalogRule $catalogPriceRule,
        CatalogProductSimple $product,
        CatalogProductView $pageCatalogProductView
    ) {
        $pageCatalogProductView->init($product);
        $pageCatalogProductView->open();
        $productPrice = $product->getPrice();
        $discountAmount = $catalogPriceRule->getDiscountAmount();
        $expectedSpecialPrice = $productPrice - $discountAmount;
        $actualSpecialPrice = $pageCatalogProductView->getViewBlock()->getProductPrice()['price_special_price'];
        \PHPUnit_Framework_Assert::assertEquals(
            $expectedSpecialPrice,
            $actualSpecialPrice,
            'Wrong special price is displayed.'
            . "\nExpected: " . $expectedSpecialPrice
            . "\nActual: " . $actualSpecialPrice
        );
    }

    /**
     * Text of catalog price rule visibility on product page (frontend)
     *
     * @return string
     */
    public function toString()
    {
        return 'Displayed catalog price rule data on product page(frontend) equals to passed from fixture.';
    }
}
