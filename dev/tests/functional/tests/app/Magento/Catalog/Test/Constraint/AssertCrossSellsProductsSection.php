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
use Mtf\Fixture\InjectableFixture;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertCrossSellsProductsSection
 * Assert that product is displayed in cross-sell section
 */
class AssertCrossSellsProductsSection extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that product is displayed in cross-sell section
     *
     * @param Browser $browser
     * @param CheckoutCart $checkoutCart
     * @param CatalogProductSimple $product
     * @param CatalogProductView $catalogProductView
     * @param InjectableFixture[] $relatedProducts
     * @return void
     */
    public function processAssert(
        Browser $browser,
        CheckoutCart $checkoutCart,
        CatalogProductSimple $product,
        CatalogProductView $catalogProductView,
        array $relatedProducts
    ) {
        $checkoutCart->open();
        $checkoutCart->getCartBlock()->clearShoppingCart();

        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->addToCart($product);
        $errors = [];
        foreach ($relatedProducts as $relatedProduct) {
            if (!$checkoutCart->getCrosssellBlock()->verifyProductCrosssell($relatedProduct)) {
                $errors[] = 'Product \'' . $relatedProduct->getName() . '\' is absent in cross-sell section.';
            }
        }

        \PHPUnit_Framework_Assert::assertEmpty($errors, implode(" ", $errors));
    }

    /**
     * Text success product is displayed in cross-sell section
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is displayed in cross-sell section.';
    }
}
