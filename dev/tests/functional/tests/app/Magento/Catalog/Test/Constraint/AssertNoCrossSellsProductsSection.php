<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Client\Browser;
use Mtf\Constraint\AbstractConstraint;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertNoCrossSellsProductsSection
 * Assert that product is not displayed in cross-sell section
 */
class AssertNoCrossSellsProductsSection extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that product is not displayed in cross-sell section
     *
     * @param Browser $browser
     * @param CatalogProductSimple $product
     * @param array $sellingProducts
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function processAssert(
        Browser $browser,
        CatalogProductSimple $product,
        array $sellingProducts,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart
    ) {
        $checkoutCart->open();
        $checkoutCart->getCartBlock()->clearShoppingCart();

        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->addToCart($product);
        foreach ($sellingProducts as $sellingProduct) {
            \PHPUnit_Framework_Assert::assertFalse(
                $checkoutCart->getCrosssellBlock()->verifyProductCrosssell($sellingProduct),
                'Product \'' . $sellingProduct->getName() . '\' is exist in cross-sell section.'
            );
        }
    }

    /**
     * Text success product is not displayed in cross-sell section
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is not displayed in cross-sell section.';
    }
}
