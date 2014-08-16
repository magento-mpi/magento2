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
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertProductInCart
 */
class AssertProductInCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assertion that the product is correctly displayed in cart
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    public function processAssert(
        CatalogProductView $catalogProductView,
        FixtureInterface $product,
        CheckoutCart $checkoutCart
    ) {
        // Add product to cart
        $catalogProductView->init($product);
        $catalogProductView->open();
        $catalogProductView->getViewBlock()->fillOptions($product);
        $catalogProductView->getViewBlock()->clickAddToCart();

        // Check price
        $this->assertOnShoppingCart($product, $checkoutCart);
    }

    /**
     * Assert prices on the shopping cart
     *
     * @param FixtureInterface $product
     * @param CheckoutCart $checkoutCart
     * @return void
     */
    protected function assertOnShoppingCart(FixtureInterface $product, CheckoutCart $checkoutCart)
    {
        $customOptions = $product->getCustomOptions();
        $checkoutData = $product->getCheckoutData();
        $checkoutCustomOptions = isset($checkoutData['custom_options']) ? $checkoutData['custom_options'] : [];
        $fixturePrice = $product->getPrice();
        $cartItem = $checkoutCart->getCartBlock()->getCartItem($product);
        $formPrice = $cartItem->getPrice();

        if ($groupPrice = $product->getGroupPrice()) {
            $groupPrice = reset($groupPrice);
            $fixturePrice = $groupPrice['price'];
        }
        if ($specialPrice = $product->getSpecialPrice()) {
            $fixturePrice = $specialPrice;
        }
        $fixtureActualPrice = $fixturePrice;

        foreach ($checkoutCustomOptions as $checkoutOption) {
            $attributeKey = $checkoutOption['title'];
            $optionKey = $checkoutOption['value'];
            $option = $customOptions[$attributeKey]['options'][$optionKey];

            if ('Fixed' == $option['price_type']) {
                $fixtureActualPrice += $option['price'];
            } else {
                $fixtureActualPrice += ($fixturePrice / 100) * $option['price'];
            }
        }

        \PHPUnit_Framework_Assert::assertEquals(
            number_format($fixtureActualPrice, 2),
            $formPrice,
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
        return 'Product is correctly displayed in cart.';
    }
}
