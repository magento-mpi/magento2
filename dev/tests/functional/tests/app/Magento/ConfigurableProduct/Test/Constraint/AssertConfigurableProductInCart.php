<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\ConfigurableProduct\Test\Page\Product\CatalogProductView;
use Magento\ConfigurableProduct\Test\Fixture\ConfigurableProductInjectable;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertConfigurableProductInCart
 */
class AssertConfigurableProductInCart extends AbstractConstraint
{
    /**
     * Assertion that the product is correctly displayed in cart
     *
     * @param CatalogProductView $catalogProductView
     * @param CheckoutCart $checkoutCart
     * @param ConfigurableProductInjectable $product
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        CheckoutCart $checkoutCart,
        ConfigurableProductInjectable $product
    ) {
        $catalogProductView->init($product);
        $catalogProductView->open();
        $catalogProductView->getViewBlock()->addToCart($product);

        $price = $checkoutCart->getCartBlock()->getCartItem($product)->getPrice();
        \PHPUnit_Framework_Assert::assertEquals(
            $product->getCheckoutData()['checkoutItemForm']['price'],
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

