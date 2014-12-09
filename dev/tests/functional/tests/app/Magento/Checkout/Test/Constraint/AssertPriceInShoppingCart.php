<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\Constraint;

use Mtf\Constraint\AbstractAssertForm;
use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Fixture\FixtureInterface;
use Magento\Checkout\Test\Fixture\Cart;
use Magento\Checkout\Test\Fixture\Cart\Items;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertPriceInShoppingCart
 * Assert that price in the shopping cart equals to expected price from data set
 */
class AssertPriceInShoppingCart extends AbstractAssertForm
{
    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert that price in the shopping cart equals to expected price from data set
     *
     * @param CheckoutCart $checkoutCart
     * @param Cart $cart
     * @return void
     */
    public function processAssert(CheckoutCart $checkoutCart, Cart $cart)
    {
        $checkoutCart->open();
        /** @var Items $sourceProducts */
        $sourceProducts = $cart->getDataFieldConfig('items')['source'];
        $products = $sourceProducts->getProducts();
        $items = $cart->getItems();
        $productsData = [];
        $cartData = [];

        foreach ($items as $key => $item) {
            /** @var CatalogProductSimple $product */
            $product = $products[$key];
            $productName = $product->getName();
            /** @var FixtureInterface $item */
            $checkoutItem = $item->getData();
            $cartItem = $checkoutCart->getCartBlock()->getCartItem($product);

            $productsData[$productName] = [
                'price' => $checkoutItem['price']
            ];
            $cartData[$productName] = [
                'price' => $cartItem->getPrice()
            ];
        }

        $error = $this->verifyData($productsData, $cartData, true);
        \PHPUnit_Framework_Assert::assertEmpty($error, $error);
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Price in the shopping cart equals to expected price from data set.';
    }
}
