<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Mtf\Client\Browser;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertConfigurableProductInCart
 * Assertion that the product is correctly displayed in cart
 */
class AssertConfigurableProductInCart extends AbstractConstraint
{
    /**
     * Assertion that the product is correctly displayed in cart
     *
     * @param Browser $browser
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param ConfigurableProductInjectable $product
     * @return void
     */
    public function processAssert(
        Browser $browser,
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        ConfigurableProductInjectable $product
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');

        $catalogProductView->getViewBlock()->addToCart($product);

        $checkoutData = $product->getCheckoutData();
        $price = $checkoutCart->getCartBlock()->getCartItem($product)->getPrice();
        \PHPUnit_Framework_Assert::assertEquals(
            $checkoutData['checkoutItemForm']['price'],
            $price,
            'Product price in shopping cart is not correct.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product price in shopping cart is correct.';
    }
}
